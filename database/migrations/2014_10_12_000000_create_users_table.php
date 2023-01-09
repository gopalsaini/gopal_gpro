<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email')->nullable();
			$table->string('phone_code', 100)->nullable();
			$table->string('mobile', 100)->nullable();
			$table->enum('status',['0','1'])->default('0')->comment('0=>Pending,1=>Active');
			$table->enum('user_type',['1','2'])->default('2')->comment('1=>Admin,2=>User');
            $table->string('password')->nullable();
            $table->foreignId('designation_id')->constrained('designations', 'id')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}