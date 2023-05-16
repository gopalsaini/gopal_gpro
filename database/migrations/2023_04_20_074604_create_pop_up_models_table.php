<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePopUpModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pop_up_models', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->longtext('sp_description')->nullable();
            $table->longtext('pt_description')->nullable();
            $table->longtext('fr_description')->nullable();
            $table->longtext('en_description')->nullable();
            $table->enum('status',['Pending','Approve','Reject'])->default('Pending');  
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pop_up_models');
    }
}
