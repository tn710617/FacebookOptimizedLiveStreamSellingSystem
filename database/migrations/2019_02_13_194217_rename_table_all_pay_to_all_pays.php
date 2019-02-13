<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameTableAllPayToAllPays extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('all_pay', function (Blueprint $table) {
            Schema::rename('all_pay', 'all_pays');
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
        Schema::table('all_pay', function (Blueprint $table) {
            Schema::rename('all_pays', 'all_pay');
            //
        });
    }
}
