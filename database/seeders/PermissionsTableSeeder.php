<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'admin_access',
            ],
            [
                'name' => 'agent_access',
            ],
            [
                'name' => 'player_access',
            ],
            [
                'name' => 'PlayerList',
            ],
            [
                'name' => 'PlayerChangePassword',
            ],
            [
                'name' => 'PlayerCreate',
            ],
            [
                'name' => 'PlayerEdit',
            ],
            [
                'name' => 'PlayerDelete',
            ],
            [
                'name' => 'BanPlayer',
            ],
            [
                'name' => 'BanAgent',
            ],
            [
                'name' => 'AgentList',
            ],
            [
                'name' => 'AgentCreate',
            ],
            [
                'name' => 'AgentEdit',
            ],
            [
                'name' => 'AgentDelete',
            ],
            [
                'name' => 'AgentChangePassword',
            ],
            [
                'name' => 'TransferLog',
            ],
            [
                'name' => 'Deposit',
            ],
            [
                'name' => 'Withdraw',
            ],
            [
                'name' => 'Bank',
            ],
            [
                'name' => 'SubAgentCreate',
            ],
            [
                'name' => 'PlayerReport',
            ],
            [
                'name' => 'AgentReport'
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission['name']
            ]);
        }
    }
}
