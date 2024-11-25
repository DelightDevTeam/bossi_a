<?php

namespace App\Http\Controllers\Admin\Agent;

use App\Enums\TransactionName;
use App\Enums\TransactionType;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubAgentRequest;
use App\Http\Requests\TransferLogRequest;
use App\Models\Admin\Permission;
use App\Models\Admin\TransferLog;
use App\Models\PaymentType;
use App\Models\User;
use App\Services\WalletService;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class SubAgentController extends Controller
{
    private const AGENT_ROLE = 3;

    public function index(): View
    {
        if (! Gate::allows('agent_index')) {
            abort(403);
        }

        //kzt
        $users = User::with('roles')
            ->whereHas('roles', function ($query) {
                $query->where('role_id', self::AGENT_ROLE);
            })
            ->where('agent_id', auth()->id())
            ->orderBy('id', 'desc')
            ->get();

        //kzt
        return view('admin.agent.sub_agent_index', compact('users'));
    }


    public function create()
    {
        abort_if(
            Gate::denies('sub_agent_create'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden |You cannot  Access this page because you do not have permission'
        );
        $agent_name = $this->generateRandomString();

        return view('admin.agent.sub_agent_create', compact('agent_name'));
    }

    private function generateRandomString()
    {
        $randomNumber = mt_rand(10000000, 99999999);

        return 'SubA'.$randomNumber;
    }

    public function store(SubAgentRequest $request): RedirectResponse
{
    if (! Gate::allows('sub_agent_create')) {
        abort(403);
    }

    try {
        DB::beginTransaction();

        // Get the parent agent's data
        $parentAgent = Auth::user(); // The authenticated parent agent

        // Prepare sub-agent data
        $userPrepare = [
            'name' => $request->input('name'),
            'password' => Hash::make($request->input('password')),
            'agent_id' => $parentAgent->id, // Reference to the parent agent
            'parent_agent_name' => $parentAgent->name, // Add parent agent name or other data if needed
            'type' => UserType::SubAgent,
        ];

        // Create the sub-agent user
        $agent = User::create($userPrepare);
        $agent->roles()->sync(self::AGENT_ROLE);

        // Process selected permissions
        $selectedPermissions = $request->input('permissions', []);
        $permissionIds = Permission::whereIn('title', $selectedPermissions)->pluck('id')->toArray();
        $agent->permissions()->sync($permissionIds);

        DB::commit();

        return redirect()->route('admin.sub-agent.index')
            ->with('success', 'Sub-agent created successfully!');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->with('error', 'Failed to create sub-agent: ' . $e->getMessage());
    }
}


    // public function store(SubAgentRequest $request): RedirectResponse
    // {
    //     if (! Gate::allows('sub_agent_create')) {
    //         abort(403);
    //     }

    //     // Validate the form inputs
    //     $inputs = $request->validated();

    //     // Prepare user data
    //     $userPrepare = array_merge(
    //         $inputs,
    //         [
    //             'password' => Hash::make($inputs['password']),
    //             'agent_id' => Auth::id(),
    //             'type' => UserType::SubAgent,
    //         ]
    //     );

    //     try {
    //         DB::beginTransaction();

    //         // Create the sub-agent user
    //         $agent = User::create($userPrepare);
    //         $agent->roles()->sync(self::AGENT_ROLE);

    //         // Process permissions (radio selection - single permission or extend for multiple if required)
    //         $selectedPermissions = $request->input('permissions', []); // Get the selected permissions
    //         if (! is_array($selectedPermissions)) {
    //             $selectedPermissions = [$selectedPermissions]; // Ensure it's an array
    //         }

    //         // Fetch permission IDs from database
    //         $permissionIds = Permission::whereIn('title', $selectedPermissions)->pluck('id')->toArray();

    //         // Sync permissions with the sub-agent
    //         $agent->permissions()->sync($permissionIds);

    //         DB::commit();

    //         return redirect()->back()
    //             ->with('success', 'Agent created successfully')
    //             ->with('password', $request->password)
    //             ->with('username', $agent->name);
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         return redirect()->back()
    //             ->with('error', 'Failed to create agent: '.$e->getMessage());
    //     }
    // }
}