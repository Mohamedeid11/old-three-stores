<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductTimelinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_timelines', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product');
            $table->integer('color')->nullable();
            $table->integer('size')->nullable();
            $table->integer('admin');
            $table->integer('order');
            $table->integer('order_type');
            $table->integer('qty')->nullable();
            $table->text('text')->nullable();
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
        Schema::dropIfExists('product_timelines');
    }
}
