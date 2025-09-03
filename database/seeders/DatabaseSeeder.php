<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roleAdmin = Role::firstOrCreate(['name' => config('roles.ADMIN')]);
        $roleUser  = Role::firstOrCreate(['name' => config('roles.USER')]);

        $admin = User::firstOrCreate(
            ['email' => 'admin@grtech.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole($roleAdmin);

        $user = User::firstOrCreate(
            ['email' => 'user@grtech.com'],
            [
                'name' => 'User',
                'password' => Hash::make('password'),
            ]
        );
        $user->assignRole($roleUser);

        Company::factory()->count(15)->create();
        Employee::factory()->count(20)->create(function () {
            return [
                'company_id' => Company::inRandomOrder()->first()->id,
            ];
        });
    }
}
