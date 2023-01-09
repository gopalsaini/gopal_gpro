<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStageFieldsInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('dob')->nullable()->after('gender');
            $table->string('citizenship')->nullable()->after('dob');
			$table->enum('marital_status', ['Married', 'Unmarried'])->nullable()->comment('Unmarried=>Unmarried, Married=>Married')->after('citizenship');
            $table->longtext('contact_address')->nullable()->after('marital_status');
            $table->string('contact_zip_code')->nullable()->after('contact_address');
            $table->integer('contact_country_id')->nullable()->after('contact_zip_code');
            $table->integer('contact_state_id')->nullable()->after('contact_country_id');
            $table->integer('contact_city_id')->nullable()->after('contact_state_id');
            $table->string('contact_business_number')->nullable()->after('contact_city_id');
            $table->string('contact_whatsapp_number')->nullable()->after('contact_business_number');
            $table->string('ministry_name')->nullable()->after('contact_whatsapp_number');
            $table->longtext('ministry_address')->nullable()->after('ministry_name');
            $table->string('ministry_zip_code')->nullable()->after('ministry_address');
            $table->integer('ministry_country_id')->nullable()->after('ministry_zip_code');
            $table->integer('ministry_state_id')->nullable()->after('ministry_country_id');
            $table->integer('ministry_city_id')->nullable()->after('ministry_state_id');
            $table->integer('stage')->default('0')->after('ministry_city_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
