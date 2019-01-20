<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStreamingItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('streaming_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id');
            $table->integer('remaining_quantity');
            $table->integer('sold_quantity')->default(0);
            $table->integer('channel_id');
            $table->dateTime('started_at');
            $table->dateTime('ended_at')->nullable();
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
        Schema::dropIfExists('streaming_items');
    }
}
