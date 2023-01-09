<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pricings', function (Blueprint $table) {
            $table->id();
            $table->string('country_name');
            $table->decimal('base_price', 18,2)->default('0.00');
            $table->decimal('twin_sharing_per_person_deluxe_room', 18,2)->default('0.00');
            $table->decimal('early_bird_cost', 18,2)->default('0.00');
            $table->decimal('both_are_trainers_deluxe_room_early_bird', 18,2)->default('0.00');
            $table->decimal('both_are_trainers_deluxe_room_after_early_bird', 18,2)->default('0.00');
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
        Schema::dropIfExists('pricings');
    }
}
