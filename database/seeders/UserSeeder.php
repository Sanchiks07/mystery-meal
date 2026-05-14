<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Sanija',
            'email' => 'sanija@gmail.com',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'name' => 'Tīna Keita',
            'email' => 'tkeita@gmail.com',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'name' => 'Leons',
            'email' => 'leons@gmail.com',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'name' => 'Bob',
            'email' => 'bob@gmail.com',
            'password' => Hash::make('password123'),
        ]);
        
        User::create([
            'name' => 'Sabīne',
            'email' => 'sbaine@gmail.com',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'name' => 'Niks',
            'email' => 'niks@gmail.com',
            'password' => Hash::make('password123'),
        ]);
        
        User::create([
            'name' => 'Kostja',
            'email' => 'kostja@gmail.com',
            'password' => Hash::make('password123'),
        ]);
    }
}