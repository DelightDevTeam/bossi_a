<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $AdminRole = Role::find(1);
        $AgentRole = Role::find(2);
        $PlayerRole = Role::find(4);
        $systemRole = Role::find(5);
        User::find(1)->assignRole($AdminRole);
        User::find(2)->assignRole($AgentRole);
        User::find(3)->assignRole($PlayerRole);
        User::find(4)->assignRole($systemRole);
        User::find(2)->givePermissionTo($AgentRole->permissions);
        User::find(3)->givePermissionTo($PlayerRole->permissions);
        User::find(4)->givePermissionTo($systemRole->permissions);   
    }
}
