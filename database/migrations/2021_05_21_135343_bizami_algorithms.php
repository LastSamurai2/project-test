<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BizamiAlgorithms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('bizami_algorithm')) {
            Schema::create('bizami_algorithm', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('description');
                $table->string('type');
                $table->decimal('mediana_multiplier_for_picks');
                $table->decimal('seasonability_multiplier');
                $table->decimal('picks_threshold');
                $table->decimal('min_cycle');
                $table->decimal('wd1_additional_minimum_condition');
                $table->decimal('wd2_cycle_extended');
                $table->decimal('wd2_reaction_value_threshold');
            });
        }

        $algorithms = \App\Models\Bizami\Algorithm::all();
        if ($algorithms->count() == 0) {
            $this->createBaseAlgorithm();
        }

        if (!Schema::hasTable('bizami_precalculation')) {
            Schema::create('bizami_precalculation', function (Blueprint $table) {
                $table->id();
                $table->string('product_id')->unique();
                $table->string('provider');
                $table->decimal('rotation_qty');
                $table->decimal('rotation_value');
                $table->decimal('seasonability');
                $table->decimal('mediana');
                $table->smallInteger('status')->default(0);
                $table->decimal('value');
                $table->index('product_id');
                $table->index('provider');
            });
        }

        DataflowsManager::createSchedule(
            'bizami_precalculate',
            [
                'name' => 'Bizami - PreCalculate',
                'status' => \App\Models\Dataflows\Schedule::STATUS_DISABLED,
                'profile_class' => 'App\Dataflows\Profile\Bizami\PreCalculate',
                'schedule' => '0 0 * * *',
            ],
            true
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DataflowsManager::deleteSchedule('bizami_precalculate');
        Schema::dropIfExists('bizami_algorithm');
        Schema::dropIfExists('bizami_precalculation');
    }

    /**
     *
     */
    protected function createBaseAlgorithm()
    {
        $cycles = [
            'd' => [
                'to' => 3000,
                'value' => 65,
            ],
            'c' => [
                'from' => 3000,
                'to' => 6000,
                'value' => 37,
            ],
            'b' => [
                'from' => 6000,
                'to' => 11000,
                'value' => 24,
            ],
            'a' => [
                'from' => 11000,
                'value' => 18,
            ],
        ];

        $baseAlgorithm = new \App\Models\Bizami\Algorithm();
        $baseAlgorithm->name = 'Base';
        $baseAlgorithm->description = 'Base algorithm parameters';
        $baseAlgorithm->type = '';
        $baseAlgorithm->mediana_multiplier_for_picks = 10;
        $baseAlgorithm->seasonability_multiplier = 0.25;
        $baseAlgorithm->picks_threshold = 4;
        $baseAlgorithm->min_cycle = 15;
        $baseAlgorithm->wd1_additional_minimum_condition = 20;
        $baseAlgorithm->wd2_cycle_extended = 0.7;
        $baseAlgorithm->wd2_reaction_value_threshold = 400;
        $baseAlgorithm->save();
    }
}
