<?php

namespace App\Http\Controllers\Admin\Agent;

use App\Enums\TransactionName;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubAgentRequest;
use App\Models\Admin\Permission;
use App\Models\User;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;


class SubAgentController extends Controller
{
    private const SUB_AGENT_ROLE = 3;

    public function index(): View
    {
        if (!Gate::allows('SubAgentCreate')) {
            abort(403);
        }

        //kzt
        $users = User::with('roles')
            ->whereHas('roles', function ($query) {
                $query->where('role_id', self::SUB_AGENT_ROLE);
            })
            ->where('agent_id', auth()->id())
            ->orderBy('id', 'desc')
            ->get();
            dd($users);
        //kzt
        return view('admin.sub_agent.index', compact('users'));
    }

    public function create()
    {
        abort_if(
            Gate::denies('SubAgentCreate'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden |You cannot  Access this page because you do not have permission'
        );
        $agent_name = $this->generateRandomString();
        $permissions = Permission::whereNotIn('title', ['admin_access', 'agent_access', 'player_access', 'PlayerCreate' ,'SubAgentCreate'])->get();

        return view('admin.sub_agent.create', compact('agent_name', 'permissions'));
    }

    private function generateRandomString()
    {
        $randomNumber = mt_rand(10000000, 99999999);

        return 'SubA' . $randomNumber;
    }


    public function store(SubAgentRequest $request): RedirectResponse
    {
        try{
            $userPrepare = [
                'user_name' => $request->user_name,
                'name' => $request->name,
                'phone' => $request->phone,
                'password' => Hash::make($request->input('password')),
                'agent_id' => Auth::id(),
                'type' => UserType::SubAgent,
            ];
    
            $agent = User::create($userPrepare);
            
            $agent->syncPermissions([
                "AgentEdit",
                "AgentDelete",
                "Player W/L Report",
                "Agent W/L Report"
            ]);
    
            return redirect()->route('admin.subagent.index')
                ->with('success', 'Sub-agent created successfully!');
        }catch(Exception $e)
        {

            return redirect()->route('admin.subagent.index')
                ->with('error', 'Sub-agent created successfully!');
        }

    }
}
