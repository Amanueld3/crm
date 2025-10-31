<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $user = User::whereHas('roles', function ($query) {
            $query->where('name', 'super_admin');
        })->where('email', 'superadmin@admin.com')->first();
        if (empty($user)) {
            $superAdmin = User::updateOrCreate([
                'email' => 'superadmin@admin.com',
            ], [
                'username' => 'superadmin',
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]);

            $superAdmin->assignRole('super_admin');
        }
    }
}
