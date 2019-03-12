<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddToBeCompletedDateInAllPayColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('all_pays', function (Blueprint $table) {
            $table->dateTime('to_be_completed_date')->nullable();
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
            $table->dropColumn('to_be_completed_date');
        });
    }
}
