<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayPalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_pals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('payment_service_id');
            $table->string('txn_id')->nullable();
            $table->string('txn_type')->nullable();
            $table->string('mc_currency')->nullable();
            $table->string('merchant_trade_no');
            $table->dateTime('payment_date')->nullable();
            $table->integer('total_amount');
            $table->string('trade_desc');
            $table->string('item_name');
            $table->boolean('status')->default(0);
            $table->dateTime('expiry_time')->nullable();
            $table->integer('user_id');
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
        Schema::dropIfExists('pay_pals');
    }
}
