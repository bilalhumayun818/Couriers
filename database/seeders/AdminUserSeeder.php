<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (! User::where('email', 'admin@gmail.com')->exists()) {
            User::where('email', 'admin@gail.com')->update(['email' => 'admin@gmail.com']);
        }

        User::firstOrCreate(['email' => 'admin@gmail.com'], [
            'name' => 'Administrator',
            'role' => 'Admin',
            'password' => '12345678',
        ]);
    }
}
