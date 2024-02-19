<?php

use Illuminate\Database\Seeder;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->insert([
            "name" => "Admin",
            "user_name" => "admin",
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123123'),
            "added_by" => 1
        ]);
    }
}
