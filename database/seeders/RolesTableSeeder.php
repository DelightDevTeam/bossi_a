<?php

namespace Database\Seeders;

use App\Models\Admin\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [

                'name' => 'Admin',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [

                'name' => 'Agent',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [

                'name' => 'SubAgent',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [

                'name' => 'Player',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [

                'name' => 'SystemWallet',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ];

        Role::insert($roles);
    }
}
