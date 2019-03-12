<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThirdPartyPaymentServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_party_payment_services', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
        DB::statement('INSERT INTO `third_party_payment_services` (`id`, `name`, `created_at`, `updated_at`)
VALUES
	(1,\'AllPay\',\'2019-02-20 06:50:18\',\'2019-02-20 06:50:18\'),
	(2,\'PayPal\',\'2019-02-20 06:50:31\',\'2019-02-20 06:50:31\'),
	(3,\'NewPayPal\',\'2019-03-06 17:08:10\',\'2019-03-06 17:08:13\');
');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('third_party_payment_services');
    }
}
