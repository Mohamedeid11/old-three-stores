<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSellOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sell_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client');
            $table->string('order_number')->nullable();
            $table->double('total_price');
            $table->string('address')->nullable();
            $table->integer('city')->default(0);
            $table->integer('country')->default(0);
            $table->integer('shipping_method')->default(0);
            $table->text('note')->nullable();
            $table->integer('pay_method')->default(0);
            $table->integer('status')->default(0);
            $table->integer('added_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->integer('deleted_by')->default(0);
            $table->integer('delivered_by')->default(0);
            $table->integer('active')->default(1);
            $table->integer('hide')->default(0);
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
        Schema::dropIfExists('sell_orders');
    }
}
