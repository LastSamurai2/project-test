<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BizamiAlgorithmsUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bizami_algorithm', function (Blueprint $table) {
            $table->decimal('cycle_a');
            $table->decimal('cycle_b');
            $table->decimal('cycle_c');
            $table->decimal('cycle_d');
            $table->decimal('r2_a_b');
            $table->decimal('r2_b_c');
            $table->decimal('r2_c_d');
            $table->decimal('mediana_multiplier_for_zmm');
        });

        /**
         * remove all algorythms, they will need to be recreated
         */
        \App\Models\Bizami\Algorithm::query()->truncate();
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
