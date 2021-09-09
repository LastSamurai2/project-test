<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Bizami\Algorithm;

class BizamiCreateAlgorithms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\Bizami\Algorithm::query()->truncate();

        $fields = [
            'name',
            'description',
            'type',
            'min_cycle',
            'cycle_a',
            'cycle_b',
            'cycle_c',
            'cycle_d',
            'r2_a_b',
            'r2_b_c',
            'r2_c_d',
            'mediana_multiplier_for_picks',
            'seasonability_multiplier',
            'picks_threshold',
            'wd1_additional_minimum_condition',
            'wd2_cycle_extended',
            'wd2_reaction_value_threshold',
            'mediana_multiplier_for_zmm',
        ];

        $agorythms = [
            ['Podstawowy'  ,           '', Algorithm::TYPE_SUPPLIER,   15, 18, 24, 37, 65, 11, 6, 3, 10, 0.20, 5, 20, 0.7, 400, 1],
            ['opcja + 8dni',           '', Algorithm::TYPE_SUPPLIER,   20, 25, 25, 37, 70, 11, 6, 3, 12, 0.20, 5, 20, 0.7, 400, 1],
            ['opcja + 15dni',          '', Algorithm::TYPE_SUPPLIER,   28, 32, 40, 50, 60, 11, 6, 3, 12, 0.20, 5, 20, 0.7, 400, 1],
            ['opcja + 25dni',          '', Algorithm::TYPE_SUPPLIER,   36, 42, 50, 60, 80, 11, 6, 3, 12, 0.20, 5, 20, 0.7, 400, 1],
            ['opcja + 40dni',          '', Algorithm::TYPE_SUPPLIER,   44, 59, 70, 80, 100, 11, 6, 3, 12, 0.20, 5, 20, 0.7, 400, 1],
            ['opcja + 60dni',          '', Algorithm::TYPE_SUPPLIER,   50, 76, 89, 90, 120, 11, 6, 3, 12, 0.20, 5, 20, 0.7, 400, 1],
            ['GD system n 1',          '', Algorithm::TYPE_SUPPLIER,   28, 44, 45, 55, 70, 11, 6, 3, 12, 0.20, 5, 20, 0.7, 400, 2],
            ['GD system n 2',          '', Algorithm::TYPE_SUPPLIER,   28, 54, 55, 68, 90, 11, 6, 3, 12, 0.18, 5, 20, 0.7, 400, 1],
            ['GD system n 3',          '', Algorithm::TYPE_SUPPLIER,   28, 64, 65, 75, 90, 11, 6, 3, 12, 0.15, 5, 20, 0.7, 400, 1],
            ['Dostawca analityczny',   '', Algorithm::TYPE_SUPPLIER,   28, 32, 40, 50, 60, 11, 6, 3, 12, 0.20, 5, 20, 0.8, 400, 1],
            ['oddziałowy',             '', Algorithm::TYPE_DEPARTMENT, 18, 10, 25, 65, 120, 5.5, 2.8, 1.1, 8, 0.15, 3, 25, 0.7, 200, 1],
            ['oddziałowy II',          '', Algorithm::TYPE_DEPARTMENT, 19, 12, 25, 65, 120, 5.5, 2.8, 1.1, 8, 0.15, 3, 25, 0.7, 200, 1],
            ['oddział ON,BD',          '', Algorithm::TYPE_DEPARTMENT, 21, 10, 33, 65, 120, 5.5, 2.8, 1.1, 8, 0.15, 3, 25, 0.7, 200, 1],
            ['oddziałowy analityczny', '', Algorithm::TYPE_DEPARTMENT, 21, 10, 33, 65, 120, 5.5, 2.8, 1.1, 8, 0.15, 3, 25, 0.7, 200, 1],
        ];

        foreach ($agorythms as $algoryhmData) {
            $a = new \App\Models\Bizami\Algorithm();
            foreach ($fields as  $key => $field) {
                $a->$field = $algoryhmData[$key];
            }
            $a->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \App\Models\Bizami\Algorithm::query()->truncate();
    }
}
