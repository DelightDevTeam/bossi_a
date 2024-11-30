<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

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
            ],
            [

                'name' => 'Agent',
            ],
            [

                'name' => 'SubAgent',
            ],
            [

                'name' => 'Player',
            ],
            [

                'name' => 'SystemWallet',
            ],

        ];

        foreach($roles as $role)
        {
            Role::create([
                'name' => $role['name']
            ]);
        }
    }
}
