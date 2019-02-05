<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNullableTypeOnExpiryTimeInPaymentServiceOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_service_orders', function (Blueprint $table) {
            $table->dateTime('expiry_time')->nullable()->change();
            //
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
            $table->dateTime('expiry_time')->change();
            //
        });
    }
}
