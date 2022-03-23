<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'nsvjon',
            'email' => 'y@y.y',
            'password' => password_hash('123', PASSWORD_DEFAULT),
        ]);

        \App\Models\User::factory(10)->create();
    }
}
