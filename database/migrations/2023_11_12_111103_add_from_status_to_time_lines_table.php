<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFromStatusToTimeLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('time_lines', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('to_status')->nullable()->after('id');
            $table->unsignedBigInteger('from_status')->nullable()->after('id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('time_lines', function (Blueprint $table) {
            //
        });
    }
}
