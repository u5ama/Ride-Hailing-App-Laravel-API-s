<?php

use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'panel_mode' => 1,
            'user_type' => 'Admin',
            'status' => 'Active',
            'password' => bcrypt('password')
        ]);
    }
}
