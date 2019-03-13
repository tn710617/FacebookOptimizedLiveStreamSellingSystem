<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTradeNoColumnIntoAllPayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('all_pays', function (Blueprint $table) {
            $table->string('TradeNo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('all_pays', function (Blueprint $table) {
            $table->dropColumn('TradeNo');
        });
    }
}
