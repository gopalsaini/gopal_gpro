<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChartDataDBSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chart_data_d_b_s', function (Blueprint $table) {
            $table->id();
            $table->longtext('getPayments')->nullable();
            $table->longtext('getUserByContinents')->nullable();
            $table->longtext('getUserByUserAge')->nullable();
            $table->longtext('getStages')->nullable();
            $table->longtext('getGroupRegisteredChartAjax')->nullable();
            $table->longtext('getSingleMarriedWSChartAjax')->nullable();
            $table->longtext('getMarriedWSChartAjax')->nullable();
            $table->longtext('getPastoralTrainersChartAjax')->nullable();
            $table->longtext('getPaymentChartAjax')->nullable();
            $table->longtext('getPaymentTypeChartAjax')->nullable();
            $table->longtext('getDoYouSeekPastoralTraining')->nullable();
            $table->longtext('TotalGroupRegistration')->nullable();
            $table->longtext('TotalMarriedCouples')->nullable();
            $table->longtext('SingleMarriedComing')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chart_data_d_b_s');
    }
}
