<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_offers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->foreignId('offer_id')->constrained('offers', 'id')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->decimal('initial_amount', 18, 2);
            $table->decimal('final_amount', 18, 2);
            $table->decimal('instant_discount', 18, 2);
			$table->enum('status',['0','1'])->default('1')->comment('0=>Pending,1=>Active');
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
        Schema::dropIfExists('sub_offers');
    }
}
