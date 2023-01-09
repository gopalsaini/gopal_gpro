<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmergencyContactInformationInTravelInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('travel_infos', function (Blueprint $table) {

            $table->string('mobile',200)->nullable();
            $table->string('name',200)->nullable();
            $table->longtext('remark',200)->nullable();

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
