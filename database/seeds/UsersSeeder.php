<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run Users database seeds.
     *
     * @return void
     */

    public function run()
    {


        DB::table('users')->insert([

            [
                'name' => 'admin',
                'role' => 'admin',
                'premuim' => true,
                'manual_premuim' => false,
                'email' => 'admin@easyplex.com',
                'password' => bcrypt('123456'),

            ],
            [
                'name' => 'user',
                'role' => 'user',
                'premuim' => false,
                'manual_premuim' => false,
                'email' => 'user@easyplex.com',
                'password' => bcrypt('123456')

            ]

        ]);

    }
}
