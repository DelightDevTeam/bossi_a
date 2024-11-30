<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::find(1)->syncPermissions([
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
            'PlayerReport',
            'AgentReport',
            'BanAgent'
        ]);
        Role::find(2)->syncPermissions([
            'PlayerList',
            'PlayerCreate',
            'PlayerEdit',
            'PlayerDelete',
            'TransferLog',
            'Deposit',
            'Withdraw',
            'Bank',
            'BanAgent',
            'BanPlayer',
            'PlayerReport',
            'SubAgentCreate',
            'PlayerChangePassword'
        ]);
    }
}
