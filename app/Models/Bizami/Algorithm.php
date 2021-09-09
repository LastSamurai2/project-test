<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Models\Bizami;

use App\Models\Form\Options;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * Class Parameter
 * @package App\Models\Bizami
 */
class Algorithm extends Model
{
    const TYPE_SUPPLIER = 'supplier';
    const TYPE_DEPARTMENT = 'department';

    protected $table = 'bizami_algorithm';
    /**
     * @var
     */
    protected $cyclesData;
    /**
     * @var
     */
    protected $typeOptions;

    /**
     * Algorithm constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $columns = Schema::getColumnListing($this->getTable());
        foreach($columns as $key) {
            $this->$key = 0;
        }

        $this->type = self::TYPE_SUPPLIER;
        $this->name = '';
        $this->description = '';
        parent::__construct($attributes);
    }

    /**
     * @return false
     */
    public function usesTimestamps()
    {
        return false;
    }

    /**
     * min.ilość tranakcji (okres -18;-12) aby liczyć sezonowoś
     */
    public function getMinTransactionsQtyForSeasonabilityPeriodA()
    {
        return 10;
    }

    /**
     * min.ilość tranakcji (okres -12;-10) aby liczyć sezonowość
     */
    public function getMinTransactionsQtyForSeasonabilityPeriodB()
    {
        return 30;
    }

    /**
     * @return mixed
     */
    public function getSeasonabilityMultiplier()
    {
        return $this->seasonability_multiplier;
    }

    /**
     * @return mixed
     */
    public function getMinCycle()
    {
        return $this->min_cycle;
    }

    /**
     * @return mixed
     */
    public function getPicksThreshold()
    {
        return $this->picks_threshold;
    }

    /**
     *  mnożnik (3) mediany  do liczenia sezonowości. wspólny parametr dla wszystkich algorytmów
     */
    public function getMedianaMultiplierForSeason()
    {
        return 8;
    }

    /**
     * @return mixed
     * mnożnik (1) mediany do określenia wartości granicznej pików- liczenie formuły głównej
     */
    public function getMedianaMultiplierForPicks()
    {
        return $this->mediana_multiplier_for_picks;
    }

    /**
     * mnożnik (0) mediany zamawianie wg mediany (ZMM)    Mnożnik mediany
     * @return mixed
     */
    public function getMedianaMultiplierForZmm()
    {
        return $this->mediana_multiplier_for_zmm;
    }

    /**
     * @param $value
     */
    public function getCycle($value)
    {
        $cycles = ['a_b', 'b_c', 'c_d'];
        $cycleCode = false;

        foreach ($cycles as $cycle) {
            $r2Code = 'r2_' . $cycle;
            list($cycleA, $cycleB) = explode('_', $cycle);

            $r2 = $this->$r2Code * 1000;

            if ($r2 > $value) {
                $cycleCode = 'cycle_' . $cycleB;
                break;
            }
        }

        if (!$cycleCode) {
            $cycleCode = 'cycle_' . $cycleA;
        }

        return $this->$cycleCode;
    }

    /**
     * @return mixed
     */
    public function getWD1AdditionalMinimumCondition()
    {
        return $this->wd1_additional_minimum_condition;
    }

    /**
     * @return mixed
     */
    public function getWD2CycleExtended()
    {
        return $this->wd2_cycle_extended;
    }

    /**
     * @return mixed
     */
    public function getWD2ReactionValueThreshold()
    {
        return $this->wd2_reaction_value_threshold;
    }

    /**
     *
     */
    public function getSettings()
    {
        $settings = [];
        $columns = Schema::getColumnListing($this->getTable());
        foreach($columns as $key) {
            $settings[$key] = $this->$key;
        }
        return $settings;
    }

    /**
     * @param false $asArrayWithKeys
     * @return Options
     */
    public function getTypeOptions()
    {
        if ($this->typeOptions == null) {
            $options = [
                self::TYPE_SUPPLIER => __('bizami.algorithm.type.supplier'),
                self::TYPE_DEPARTMENT => __('bizami.algorithm.type.department'),
            ];
            $this->typeOptions = new Options($options);
        }
        return $this->typeOptions;
    }
}
