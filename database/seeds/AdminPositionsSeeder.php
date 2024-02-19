<?php

use Illuminate\Database\Seeder;

class AdminPositionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admin_positions')->insert([
            "position" => "Admin"
        ]);

        DB::table('admin_positions')->insert([
            "position" => "REP"
        ]);
    }
}
