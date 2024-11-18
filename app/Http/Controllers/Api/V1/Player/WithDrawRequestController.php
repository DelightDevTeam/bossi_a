<?php

namespace App\Http\Controllers\Api\V1\Player;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WithdrawRequest;
use App\Http\Resources\HistoryResource;
use App\Models\WithDrawRequest as ModelsWithDrawRequest;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class WithDrawRequestController extends Controller
{
    use HttpResponses;

    public function withdraw(WithdrawRequest $request)
    {
        try {
            $player = Auth::user();
            if (! $player || ! Hash::check($request->password, $player->password)) {
                return $this->error('', 'လျို့ဝှက်နံပါတ်ကိုက်ညီမှု မရှိပါ။', 401);
            }
            
            if ($request->amount > $player->balanceFloat) {
                return $this->error('', 'Insufficient Balance', 401);
            }

            $withdraw = ModelsWithDrawRequest::create([
                'user_id' => $player->id,
                'agent_id' => $player->agent_id,
                'amount' => $request->amount,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'bank_id' => $request->bank_id,
            ]);

            return $this->success($withdraw, 'Withdraw Request Success');

        } catch (Exception $e) {
            $this->error('', $e->getMessage(), 401);
        }
    }

    public function log()
    {
        $withdraw = ModelsWithDrawRequest::where('user_id', Auth::id())->get();

        return $this->success(HistoryResource::collection($withdraw));
    }
}
