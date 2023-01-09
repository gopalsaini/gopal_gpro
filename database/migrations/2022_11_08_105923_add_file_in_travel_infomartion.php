<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFileInTravelInfomartion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('travel_infos', function (Blueprint $table) {

            $table->enum('logistics_picked',['Yes','No'])->default('No')->after('admin_status');
            $table->enum('logistics_dropped',['Yes','No'])->default('No')->after('logistics_picked');  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('travel_infomartion', function (Blueprint $table) {
            //
        });
    }
}
