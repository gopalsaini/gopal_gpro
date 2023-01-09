<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraFeildInFaqTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('faqs', function (Blueprint $table) {
            $table->string('sp_question',500)->nullable();
            $table->longtext('sp_answer')->nullable();
            $table->string('fr_question',500)->nullable();
            $table->longtext('fr_answer')->nullable();
            $table->string('pt_question',500)->nullable();
            $table->longtext('pt_answer')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('faqs', function (Blueprint $table) {
            //
        });
    }
}
