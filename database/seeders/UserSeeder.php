<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'test',
            'name' => 'test',
            'token' => 'test',
            'password' => bcrypt('test'),
        ]);

        User::create([
            'username' => 'test2',
            'name' => 'test2',
            'token' => 'test2',
            'password' => bcrypt('test2'),
        ]);
    }
}
