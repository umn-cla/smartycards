<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create an admin account and give
        // it super admin privileges
        $admin = User::create([
            'name' => 'SmartyCards Admin',
            "first_name" => 'SmartyCards',
            "last_name" => 'Admin',
            'email' => 'latistecharch+admin@umn.edu',
            'emplid' => '1001',
            'umndid' => 'admin',
            'password' => bcrypt(Str::random(10)),
        ]);

        $admin->assignRole(Role::SUPER_ADMIN);
    }
}
