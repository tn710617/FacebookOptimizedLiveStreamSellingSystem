<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewPayPalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_pay_pals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('payment_service_id');
            $table->string('mc_currency')->nullable();
            $table->string('merchant_trade_no');
            $table->dateTime('approve_date')->nullable();
            $table->dateTime('to_be_completed_date')->nullable();
            $table->integer('total_amount');
            $table->string('trade_desc');
            $table->integer('recipient_id');
            $table->string('item_name')->nullable();
            $table->integer('status')->default(1);
            $table->dateTime('expiry_time')->nullable();
            $table->dateTime('to_be_captured_date')->nullable();
            $table->dateTime('authorization_expiry_date')->nullable();
            $table->integer('to_be_captured_amount')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('authorization_id')->nullable();
            $table->string('capture_id')->nullable();
            $table->integer('user_id');
            $table->string('client_back_url');
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
        Schema::dropIfExists('new_pay_pals');
    }
}
