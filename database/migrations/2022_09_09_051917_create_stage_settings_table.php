<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStageSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stage_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('designation_id')->constrained('designations', 'id')->onDelete('CASCADE')->onUpdate('CASCADE');
			$table->enum('stage_zero', ['0', '1'])->default('0')->comment('0=>Inactive, 1=>Active');
			$table->enum('stage_one', ['0', '1'])->default('0')->comment('0=>Inactive, 1=>Active');
			$table->enum('stage_two', ['0', '1'])->default('0')->comment('0=>Inactive, 1=>Active');
			$table->enum('stage_three', ['0', '1'])->default('0')->comment('0=>Inactive, 1=>Active');
			$table->enum('stage_four', ['0', '1'])->default('0')->comment('0=>Inactive, 1=>Active');
			$table->enum('stage_five', ['0', '1'])->default('0')->comment('0=>Inactive, 1=>Active');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stage_settings');
    }
}
