<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreFieldsInTravelInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('travel_infos', function (Blueprint $table) {
            $table->longtext('flight_details')->after('name');
            $table->longtext('hotel_information')->after('flight_details');
            $table->longtext('return_flight_details')->nullable()->after('checkout_info');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('travel_infos', function (Blueprint $table) {
            //
        });
    }
}
