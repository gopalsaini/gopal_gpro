<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users','id')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->string('order_id');
            $table->enum('payment_by',['1','2'])->comment("1=>Razor pay,2=>Paypal");
            $table->integer('currency_id')->default('0');
            $table->string('razorpay_order_id')->nullable();
            $table->text('razorpay_paymentid')->nullable();
            $table->string('transaction_id')->nullable()->unique();
            $table->string('method')->nullable();
            $table->string('card_id')->nullable();
            $table->string('bank')->nullable();
            $table->string('wallet')->nullable();
            $table->string('vpa')->nullable();
            $table->string('bank_transaction_id')->nullable();
            $table->text('description')->nullable();
            $table->string('email')->nullable();
            $table->string('contact')->nullable();
            $table->decimal('amount',18,2)->default('0.00');
            $table->string('error_code')->nullable();
            $table->string('error_description')->nullable();
            $table->string('error_reason')->nullable();
            $table->string('paypal_token')->nullable();
            $table->string('paypal_payerid')->nullable();
            $table->integer('payment_status')->default('0');
			$table->enum('status',['0','1'])->nullable()->comment('0=>Reject, 1=>Approve');
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
        Schema::dropIfExists('transactions');
    }
}
