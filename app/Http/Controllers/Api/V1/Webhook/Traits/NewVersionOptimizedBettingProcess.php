<?php

namespace App\Http\Controllers\Api\V1\Webhook\Traits;

use App\Enums\TransactionName;
use App\Enums\WagerStatus;
use App\Http\Requests\Slot\SlotWebhookRequest;
use App\Models\Admin\GameType;
use App\Models\Admin\GameTypeProduct;
use App\Models\Admin\Product;
use App\Models\SeamlessEvent;
use App\Models\User;
use App\Models\Wager;
use App\Services\WalletService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

trait NewVersionOptimizedBettingProcess
{
    public function placeBet(SlotWebhookRequest $request)
    {
        $userId = $request->getMember()->id;

        // Try to acquire a Redis lock for the user's wallet
        $lock = Redis::set("wallet:lock:$userId", true, 'EX', 10, 'NX');  // 10-second lock

        if (! $lock) {
            return response()->json(['message' => 'The wallet is currently being updated. Please try again later.'], 409);
        }

        $event = $this->createEvent($request);

        DB::beginTransaction();
        try {
            // Validate the request
            $validator = $request->check();
            if ($validator->fails()) {
                Redis::del("wallet:lock:$userId");

                return $validator->getResponse();
            }

            $before_balance = $request->getMember()->balanceFloat;

            // Create and store the event in the database
            //$event = $this->createEvent($request);

            // Retry logic for creating wager transactions with exponential backoff
            $seamless_transactions = $this->retryOnDeadlock(function () use ($validator, $event) {
                return $this->createWagerTransactions($validator->getRequestTransactions(), $event);
            });

            // Process each seamless transaction
            foreach ($seamless_transactions as $transaction) {
                $fromUser = $request->getMember();
                $toUser = User::adminUser();  // Admin or central system wallet

                // Fetch the rate from GameTypeProduct before calling processTransfer()
                $game_type = GameType::where('code', $transaction->GameType)->first();
                $product = Product::where('code', $transaction->ProductID)->first();
                $game_type_product = GameTypeProduct::where('game_type_id', $game_type->id)
                    ->where('product_id', $product->id)
                    ->first();

                // Use the rate from GameTypeProduct or fallback to a default value
                $rate = (int) ($game_type_product->rate ?? 1);

                $meta = [
                    'wager_id' => $transaction->WagerID,               // Use object property access
                    'event_id' => $request->getMessageID(),
                    'seamless_transaction_id' => $transaction->TransactionID,  // Use object property access
                ];

                // Call processTransfer for each transaction
                $this->processTransfer(
                    $fromUser,                        // From user
                    $toUser,                          // To user (admin/system wallet)
                    TransactionName::Stake,           // Transaction name (e.g., Stake)
                    $transaction->TransactionAmount,  // Use object property access for TransactionAmount
                    $rate,                            // Use the fetched rate or default value
                    $meta                             // Meta data (wager id, event id, etc.)
                );
            }

            // Refresh balance after transactions
            $request->getMember()->wallet->refreshBalance();
            $after_balance = $request->getMember()->balanceFloat;

            DB::commit();
            Redis::del("wallet:lock::$userId");

            return response()->json([
                'balance_before' => $before_balance,
                'balance_after' => $after_balance,
                'message' => 'Bet placed successfully.',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Redis::del("wallet:lock::$userId");

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Creates wagers in chunks and inserts them along with related seamless transactions.
     */
    public function insertBets(array $bets, SeamlessEvent $event)
    {
        $chunkSize = 50; // Define the chunk size
        $batches = array_chunk($bets, $chunkSize);

        $userId = $event->user_id; // Get user_id from SeamlessEvent

        // Process chunks in a transaction to ensure data integrity
        DB::transaction(function () use ($batches, $event) {
            foreach ($batches as $batch) {
                // Call createWagerTransactions for each batch
                $this->createWagerTransactions($batch, $event);
            }
        });

        return count($bets).' bets inserted successfully.';
    }

    /**
     * Creates wagers in chunks and inserts them along with related seamless transactions.
     */
    public function createWagerTransactions(array $betBatch, SeamlessEvent $event)
    {
        $retryCount = 0;
        $maxRetries = 5;
        $userId = $event->user_id; // Get user_id from the SeamlessEvent
        $seamlessEventId = $event->id; // Get the ID of the SeamlessEvent

        // Log the start of the transaction processing
        //Log::debug("Starting createWagerTransactions for user ID: $userId, event ID: $seamlessEventId");

        // Retry logic for deadlock handling
        do {
            try {
                DB::transaction(function () use ($betBatch, $userId, $seamlessEventId) {
                    // Initialize arrays for batch inserts
                    $wagerData = [];
                    $seamlessTransactionsData = [];

                    // Log batch size being processed
                    //Log::debug('Processing bet batch of size: '.count($betBatch));

                    // Loop through each bet in the batch
                    foreach ($betBatch as $transaction) {

                        // Log transaction details
                        //Log::debug('Processing transaction', ['transaction' => $transaction]);

                        // If transaction is an instance of the RequestTransaction object, extract the data
                        if ($transaction instanceof \App\Services\Slot\Dto\RequestTransaction) {

                            // Attempt to retrieve the ActualGameTypeID from the database based on GameType
                            $gameType = GameType::where('code', $transaction->GameType)->first();
                            if ($gameType) {
                                $transaction->ActualGameTypeID = $gameType->id;
                            } else {
                                throw new \Exception('Invalid GameType: '.$transaction->GameType);
                            }

                            // Attempt to retrieve the ActualProductID from the database based on ProductID
                            $product = Product::where('code', $transaction->ProductID)->first();
                            if ($product) {
                                $transaction->ActualProductID = $product->id;
                            } else {
                                throw new \Exception('Invalid ProductID: '.$transaction->ProductID);
                            }

                            // Fetch the rate from GameTypeProduct
                            $game_type_product = GameTypeProduct::where('game_type_id', $gameType->id)
                                ->where('product_id', $product->id)
                                ->first();
                            if (! $game_type_product) {
                                throw new \Exception('GameTypeProduct combination not found.');
                            }

                            // Ensure rate is a valid integer, and provide a fallback if missing
                            $rate = (int) ($game_type_product->rate ?? 1);  // Cast rate to int with fallback value

                            // Build transaction data
                            $transactionData = [
                                'Status' => $transaction->Status,
                                'ProductID' => $transaction->ProductID,
                                'GameType' => $transaction->GameType,
                                'TransactionID' => $transaction->TransactionID,
                                'WagerID' => $transaction->WagerID,
                                'BetAmount' => $transaction->BetAmount,
                                'TransactionAmount' => $transaction->TransactionAmount,
                                'PayoutAmount' => $transaction->PayoutAmount,
                                'ValidBetAmount' => $transaction->ValidBetAmount,
                                'Rate' => $rate,  // Use the fetched rate
                                'ActualGameTypeID' => $transaction->ActualGameTypeID,
                                'ActualProductID' => $transaction->ActualProductID,
                            ];

                        } else {
                            Log::error('Invalid transaction data format', ['transaction' => $transaction]);
                            throw new \Exception('Invalid transaction data format.');
                        }

                        // Now, use the $transactionData array as expected
                        $existingWager = Wager::where('seamless_wager_id', $transactionData['WagerID'])->lockForUpdate()->first();

                        if (! $existingWager) {
                            // Collect wager data for batch insert
                            $wagerData[] = [
                                'user_id' => $userId,  // Use user_id from the SeamlessEvent
                                'seamless_wager_id' => $transactionData['WagerID'],
                                'status' => $transactionData['TransactionAmount'] > 0 ? WagerStatus::Win : WagerStatus::Lose,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }

                        // Collect seamless transaction data for batch insert
                        $seamlessTransactionsData[] = [
                            'user_id' => $userId,  // Use user_id from the SeamlessEvent
                            'wager_id' => $existingWager ? $existingWager->id : null,
                            'game_type_id' => $transactionData['ActualGameTypeID'],
                            'product_id' => $transactionData['ActualProductID'],
                            'seamless_transaction_id' => $transactionData['TransactionID'],
                            'rate' => $transactionData['Rate'],
                            'transaction_amount' => $transactionData['TransactionAmount'],
                            'bet_amount' => $transactionData['BetAmount'],
                            'valid_amount' => $transactionData['ValidBetAmount'],
                            'status' => $transactionData['Status'],
                            'seamless_event_id' => $seamlessEventId,  // Include seamless_event_id
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    // Perform batch inserts
                    if (! empty($wagerData)) {
                        //Log::debug('Inserting wager data', ['wagerData' => $wagerData]);
                        DB::table('wagers')->insert($wagerData); // Insert wagers in bulk
                    }

                    if (! empty($seamlessTransactionsData)) {
                        //Log::debug('Inserting seamless transactions data', ['seamlessTransactionsData' => $seamlessTransactionsData]);
                        DB::table('seamless_transactions')->insert($seamlessTransactionsData); // Insert transactions in bulk
                    }
                });

                //Log::debug('createWagerTransactions completed successfully for event ID: '.$seamlessEventId);
                break; // Exit the retry loop if successful

            } catch (\Illuminate\Database\QueryException $e) {
                if ($e->getCode() === '40001') { // Deadlock error code
                    Log::warning('Deadlock detected, retrying...', ['retryCount' => $retryCount]);
                    $retryCount++;
                    if ($retryCount >= $maxRetries) {
                        Log::error('Max retries reached, throwing exception.', ['error' => $e]);
                        throw $e; // Max retries reached, fail
                    }
                    sleep(1); // Wait for a second before retrying
                } else {
                    Log::error('Query exception encountered', ['error' => $e]);
                    throw $e; // Rethrow if it's not a deadlock exception
                }
            }
        } while ($retryCount < $maxRetries);
    }

    public function processTransfer(User $from, User $to, TransactionName $transactionName, float $amount, int $rate, array $meta)
    {
        $retryCount = 0;
        $maxRetries = 5;

        do {
            try {
                // Only lock the necessary rows inside the transaction
                DB::transaction(function () use ($from, $to, $amount, $transactionName, $meta) {
                    // Lock only the specific rows for the wallet that needs updating
                    $walletFrom = $from->wallet()->lockForUpdate()->firstOrFail();
                    $walletTo = $to->wallet()->lockForUpdate()->firstOrFail();

                    // Update wallet balances
                    $walletFrom->balance -= $amount;
                    $walletTo->balance += $amount;

                    // Save the updated balances
                    $walletFrom->save();
                    $walletTo->save();

                    // Perform the transfer in the wallet service (possibly outside the transaction)
                    app(WalletService::class)->transfer($from, $to, abs($amount), $transactionName, $meta);
                });

                break;  // Exit loop if successful

            } catch (\Illuminate\Database\QueryException $e) {
                if ($e->getCode() === '40001') {  // Deadlock error code
                    $retryCount++;
                    if ($retryCount >= $maxRetries) {
                        throw $e;  // Max retries reached, fail
                    }
                    sleep(1);  // Wait before retrying
                } else {
                    throw $e;  // Rethrow non-deadlock exceptions
                }
            }
        } while ($retryCount < $maxRetries);
    }

    /**
     * Retry logic for handling deadlocks with exponential backoff.
     */
    private function retryOnDeadlock(callable $callback, $maxRetries = 5)
    {
        $retryCount = 0;

        do {
            try {
                return $callback();
            } catch (\Illuminate\Database\QueryException $e) {
                if ($e->getCode() === '40001') {  // Deadlock error code
                    $retryCount++;
                    if ($retryCount >= $maxRetries) {
                        throw $e;  // Max retries reached, fail
                    }
                    sleep(pow(2, $retryCount));  // Exponential backoff
                } else {
                    throw $e;  // Rethrow non-deadlock exceptions
                }
            }
        } while ($retryCount < $maxRetries);
    }

    /**
     * Create the event in the system.
     */
    public function createEvent(SlotWebhookRequest $request): SeamlessEvent
    {
        return SeamlessEvent::create([
            'user_id' => $request->getMember()->id,
            'message_id' => $request->getMessageID(),
            'product_id' => $request->getProductID(),
            'request_time' => $request->getRequestTime(),
            'raw_data' => $request->all(),
        ]);
    }
}
