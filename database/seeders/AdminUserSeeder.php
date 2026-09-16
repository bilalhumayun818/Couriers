<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(['email' => 'admin@gail.com'], [
            'name' => 'Administrator',
            'password' => '12345678',
        ]);
    }
}
