<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_balances', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('buying_order_id')->nullable();
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->double('total_price')->default(0);
            $table->double('payment_amount')->default(0);
            $table->double('differance')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agent_balances');
    }
}
