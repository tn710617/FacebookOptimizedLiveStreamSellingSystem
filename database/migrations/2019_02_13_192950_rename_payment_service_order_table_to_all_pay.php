<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenamePaymentServiceOrderTableToAllPay extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_service_orders', function (Blueprint $table) {
            Schema::rename('payment_service_orders', 'all_pay');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_service_orders', function (Blueprint $table) {
            Schema::rename('all_pay', 'payment_service_orders');
            //
        });
    }
}
