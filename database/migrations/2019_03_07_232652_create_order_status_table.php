<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_status', function (Blueprint $table) {
            $table->integer('id');
            $table->string('status');
            $table->timestamps();
        });
        DB::statement('INSERT INTO `order_status` (`id`, `status`, `created_at`, `updated_at`)
VALUES
	(1,\'effective\',\'2019-03-08 07:30:56\',\'2019-03-08 07:31:01\'),
	(2,\'expired\',\'2019-03-08 07:31:44\',\'2019-03-08 07:31:44\'),
	(3,\'completed\',\'2019-03-08 07:32:10\',\'2019-03-08 07:32:10\'),
	(4,\'refunded\',\'2019-03-08 07:32:38\',\'2019-03-08 07:32:38\'),
	(5,\'approved\',\'2019-03-08 07:32:50\',\'2019-03-08 07:32:50\');
');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_status');
    }
}
