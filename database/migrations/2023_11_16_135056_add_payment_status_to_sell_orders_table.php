<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaymentStatusToSellOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sell_orders', function (Blueprint $table) {
            //
            $table->double('payment_amount')->nullable()->default(0)->after('id');
            $table->enum('payment_status',['paid','not_paid','partly_paid'])->nullable()->default('not_paid')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sell_orders', function (Blueprint $table) {
            //
        });
    }
}
