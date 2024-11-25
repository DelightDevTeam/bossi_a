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
        $admin_permissions = Permission::whereIn('title', [
            'admin_access',
            'agent_access',
            'agent_index',
            'agent_create',
            'agent_edit',
            'agent_delete',
            'agent_change_password_access',
            'transfer_log',
            'make_transfer',
            'game_type_access',
        ]);
        Role::findOrFail(1)->permissions()->sync($admin_permissions->pluck('id'));

        $agent_permissions = Permission::whereIn('title', [
            'agent_access',
            'agent_index',
            'agent_create',
            'agent_edit',
            'agent_delete',
            'agent_change_password_access',
            'player_index',
            'player_create',
            'player_edit',
            'player_delete',
            'transfer_log',
            'make_transfer',
            'withdraw',
            'deposit',
            'bank',
            'site_logo',
            'sub_agent_create',
            'new_player',
            'copy_player',
            'player_list',
            'edit_member',
            'change_all_status',
            'unlock_password_lock',
            'adjust_balance',
            'all_report_access',
            'win_lose_report',
            'cf_match_report',
            'transaction_history',
            'sports_match_report',
            'outstanding_report',
            'fund_in_out',
            'sports_betting_access',
            'log_access',
            'campaign_management',
            'refer_friends_program'


        ])->pluck('id');

        Role::findOrFail(2)->permissions()->sync($agent_permissions);

        $sub_agent_permissions = Permission::whereIn('title', [
            'agent_access',
            'agent_index',
            'agent_create',
            'agent_edit',
            'agent_delete',
            'agent_change_password_access',
            'player_index',
            'player_create',
            'player_edit',
            'player_delete',
            'transfer_log',
            'make_transfer',
            'withdraw',
            'deposit',
            'bank',
            'site_logo',
            'sub_agent_create',
            'new_player',
            'copy_player',
            'player_list',
            'edit_member',
            'change_all_status',
            'unlock_password_lock',
            'adjust_balance',
            'all_report_access',
            'win_lose_report',
            'cf_match_report',
            'transaction_history',
            'sports_match_report',
            'outstanding_report',
            'fund_in_out',
            'sports_betting_access',
            'log_access',
            'campaign_management',
            'refer_friends_program'


        ])->pluck('id');

        Role::findOrFail(3)->permissions()->sync($sub_agent_permissions);
    }
}