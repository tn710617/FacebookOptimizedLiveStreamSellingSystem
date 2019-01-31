<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRecipientsColumnInOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('recipient');
            $table->string('phone_code');
            $table->string('phone_number');
            $table->string('post_code');
            $table->string('country');
            $table->string('city');
            $table->string('district');
            $table->string('others');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['recipient', 'phone_code', 'phone_number', 'post_code', 'country', 'city', 'district', 'others']);
            //
        });
    }
}
