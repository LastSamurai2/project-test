<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BizamiSimulationNewColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bizami_simulation', function (Blueprint $table) {
            $table->renameColumn('algorythm_name', 'algorithm_name');
            $table->renameColumn('algorythm_settings', 'algorithm_settings');
            $table->bigInteger('algorithm_id', false, true);
            $table->integer('processed_iterations');
            $table->integer('total_iterations');
            $table->integer('calculation_progress');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
