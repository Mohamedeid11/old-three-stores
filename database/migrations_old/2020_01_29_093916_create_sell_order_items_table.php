<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSellOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sell_order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order');
            $table->integer('product');
            $table->integer('qty')->default(1);
            $table->integer('color')->default(0);
            $table->integer('size')->default(0);
            $table->double('price')->default(0);
            $table->text('note')->nullable();
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
        Schema::dropIfExists('sell_order_items');
    }
}
