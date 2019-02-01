<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentServiceOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_service_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('payment_service_id');
            $table->integer('order_relation_id');
            $table->string('MerchantID');
            $table->string('MerchantTradeNo');
            $table->dateTime('MerchantTradeDate');
            $table->integer('TotalAmount');
            $table->string('TradeDesc');
            $table->string('ItemName');
            $table->boolean('status')->default(false);
            $table->dateTime('expiry_time');
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
        Schema::dropIfExists('payment_service_orders');
    }
}
