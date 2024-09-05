<?php

namespace App\Http\Controllers\Admin\Deposit;

use App\Enums\TransactionName;
use App\Http\Controllers\Controller;
use App\Models\DepositRequest;
use App\Models\User;
use App\Models\WithDrawRequest;
use App\Services\WalletService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepositRequestController extends Controller
{
    // public function index()
    // {
    //     $deposits = DepositRequest::with(['user', 'bank', 'agent'])->where('agent_id', Auth::id())->get();

    //     return view('admin.deposit_request.index', compact('deposits'));
    // }

    public function index()
{
    // Fetch deposits for the logged-in agent and filter by their statuses
    $approvedDeposits = DepositRequest::with(['user', 'bank', 'agent'])
        ->where('agent_id', Auth::id())
        ->where('status', 1) // 1 for Approved
        ->get();

    $rejectedDeposits = DepositRequest::with(['user', 'bank', 'agent'])
        ->where('agent_id', Auth::id())
        ->where('status', 2) // 2 for Rejected
        ->get();

    $pendingDeposits = DepositRequest::with(['user', 'bank', 'agent'])
        ->where('agent_id', Auth::id())
        ->where('status', 0) // 0 for Pending
        ->get();

    return view('admin.deposit_request.index', compact('approvedDeposits', 'rejectedDeposits', 'pendingDeposits'));
}


    public function statusChangeIndex(Request $request, DepositRequest $deposit)
    {
        $request->validate([
            'status' => 'required|in:0,1,2',
            'amount' => 'required|numeric|min:0',
            'player' => 'required|exists:users,id',
        ]);

        try {
            $agent = Auth::user();
            $player = User::find($request->player);

            // Check if the status is being approved and balance is sufficient
            if ($request->status == 1 && $agent->balanceFloat < $request->amount) {
                return redirect()->back()->with('error', 'You do not have enough balance to transfer!');
            }

            // Update the deposit status
            $deposit->update([
                'status' => $request->status,
            ]);

            // Transfer the amount if the status is approved
            if ($request->status == 1) {
                app(WalletService::class)->transfer($agent, $player, $request->amount, TransactionName::DebitTransfer);
            }

            return redirect()->route('admin.agent.deposit')->with('success', 'Deposit status updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function statusChangeReject(Request $request, DepositRequest $deposit)
    {
        $request->validate([
            'status' => 'required|in:0,1,2',
        ]);

        try {
            // Update the deposit status
            $deposit->update([
                'status' => $request->status,
            ]);

            return redirect()->route('admin.agent.deposit')->with('success', 'Deposit status updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public  function view(DepositRequest $deposit)
    {
        return view('admin.deposit_request.view', compact('deposit'));
    }
}
