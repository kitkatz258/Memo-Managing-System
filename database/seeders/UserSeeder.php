<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        User::create([
            'name' => 'User',
            'username' => 'user',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);
    }
}
