<?php

use Illuminate\Database\Seeder;

class ServersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        DB::table('servers')->insert([

            [
                'name' => '480P',
                'status' => true,

            ],
            [
                'name' => '720P',
                'status' => true,

            ],


            [
                'name' => '1080P',
                'status' => true,

            ],


            [
                'name' => '4K',
                'status' => true,

            ]

        ]);

    }
}
