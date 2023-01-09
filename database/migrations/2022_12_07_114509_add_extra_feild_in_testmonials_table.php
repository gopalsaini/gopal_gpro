<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraFeildInTestmonialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->string('sp_title');
            $table->string('sp_designation');
            $table->longtext('sp_description');

            $table->string('fr_title');
            $table->string('fr_designation');
            $table->longtext('fr_description');

            $table->string('pt_title');
            $table->string('pt_designation');
            $table->longtext('pt_description');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('testimonials', function (Blueprint $table) {
            //
        });
    }
}
