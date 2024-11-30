<?php

namespace App\Http\Controllers\Admin\TransferLog;

use App\Http\Controllers\Controller;
use App\Models\Admin\TransferLog;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class TransferLogController extends Controller
{
    private const SUB_AGENT_ROLE = 3;
    
    public function __construct()
    {
        $this->middleware('permission:view role', ['only' => ['TransferLog']]);
    }

    public function index()
    {     
        if(!Gate::allows('TransferLog'))
        {
            abort(403);
        }
        $agent = $this->getAgent() ?? Auth::user();

         $transferLogs = $agent->transactions()->with('targetUser')
            ->whereIn('transactions.type', ['withdraw', 'deposit'])
            ->whereIn('transactions.name', ['credit_transfer', 'debit_transfer'])
            ->latest()->paginate();

        return view('admin.trans_log.index', compact('transferLogs'));
    }

    public function transferLog($id)
    {
        abort_if(
            Gate::denies('TransferLog') || ! $this->ifChildOfParent(request()->user()->id, $id),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden | You cannot access this page because you do not have permission'
        );

        $transferLogs = Auth::user()->transactions()->with('targetUser')
            ->whereIn('transactions.type', ['withdraw', 'deposit'])
            ->whereIn('transactions.name', ['credit_transfer', 'debit_transfer'])
            ->where('target_user_id', $id)->latest()->paginate();

        return view('admin.trans_log.detail', compact('transferLogs'));
    }

    private function isExistingAgent($userId)
    {
        $user = User::find($userId);
    
        return $user && $user->hasRole(self::SUB_AGENT_ROLE) ? $user->parent : null;
    }
    
    private function getAgent()
    {
        return $this->isExistingAgent(Auth::id());
    }
}
