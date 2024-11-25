<?php

namespace Database\Seeders;

use App\Models\Admin\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'title' => 'admin_access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'agent_access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'player_access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'player_index',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'player_create',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'player_edit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'player_delete',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'agent_index',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'agent_create',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'agent_edit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'agent_delete',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'agent_change_password_access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'transfer_log',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'make_transfer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'bank',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'withdraw',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'deposit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'site_logo',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'title' => 'sub_agent_create', // Permission for sub-agents to create other sub-agent accounts
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'title' => 'new_player',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'copy_player',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'player_list',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'edit_member',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'change_all_status',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'unlock_password_lock',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'adjust_balance',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'all_report_access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'win_lose_report',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'cf_match_report',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'transaction_history',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'sports_match_report',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'outstanding_report',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'fund_in_out',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'sports_betting_access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'log_access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'campaign_management',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'refer_friends_program',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Permission::insert($permissions);
    }
}