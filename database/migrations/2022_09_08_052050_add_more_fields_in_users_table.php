<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreFieldsInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name', 100)->nullable()->change();
            $table->string('email', 100)->unique()->nullable(false)->change();
            $table->enum('profile_update', ['0','1'])->default('0')->comment('0=>Pending,1=>Update')->after('designation_id');
            $table->timestamp('profile_updated_at')->nullable()->after('profile_update');
            $table->enum('terms_and_condition', ['0','1'])->default('0')->comment('0=>Pending,1=>Read')->after('profile_updated_at');
            $table->timestamp('status_change_at')->nullable()->after('status');
            $table->decimal('amount', 18, 2)->default('0.00')->after('terms_and_condition');
            $table->integer('payment_status')->default(0)->after('amount');
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
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
