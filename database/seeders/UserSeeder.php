<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'nama' => 'AdminSira',
            'nim' => '00000000',
            'username' => 'superadmin',
            'password' => Hash::make('password'),
            'email' => 'admin@campus.ac.id',
            'role' => 'admin',
        ]);
    }
}