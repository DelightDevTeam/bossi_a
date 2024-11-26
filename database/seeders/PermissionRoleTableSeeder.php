<?php

namespace Database\Seeders;

use App\Models\Admin\Permission;
use App\Models\Admin\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin permissions
        $admin_permissions = Permission::whereIn('name', [
            'admin_access',
            'agent_access',
            'AgentList',
            'AgentCreate',
            'AgentEdit',
            'AgentDelete',
            'AgentChangePassword',
            'TransferLog',
            'Deposit',
            'Withdraw',
            'GameTypeAccess',
            'Player W/L Report',
            'BanAgent',
        ]);
        Role::findOrFail(1)->permissions()->sync($admin_permissions->pluck('id'));

        $agent_permissions = Permission::whereIn('name', [
            'agent_access',
            'agent_index',
            'agent_create',
            'agent_edit',
            'agent_delete',
            'AgentChangePassword',
            'PlayerList',
            'PlayerCreate',
            'PlayerEdit',
            'PlayerDelete',
            'TransferLog',
            'Deposit',
            'Withdraw',
            'deposit',
            'Bank',   
            'BanAgent',
            'BanPlayer',
            'Player W/L Report',
            'Agent W/L Report'     ,
            'SubAgentCreate'   ,
            'PlayerChangePassword'

        ])->pluck('id');

        Role::findOrFail(2)->permissions()->sync($agent_permissions);
    }
}
