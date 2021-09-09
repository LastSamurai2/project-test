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
 * Class Warehouse
 * @package App\Models\Bizami
 */
class Warehouse extends Model
{
    const TYPE_MAIN = 'main';
    const TYPE_DEPARTMENT = 'department';

    protected $table = 'bizami_warehouse';

    protected $typeOptions;

    /**
     * @return false
     */
    public function usesTimestamps()
    {
        return false;
    }

    /**
     *
     */
    public function getDataFieldList()
    {
        $columns = Schema::getColumnListing($this->table);
        return $columns;
    }

    /**
     * @param false $asArrayWithKeys
     * @return Options
     */
    public function getTypeOptions()
    {
        if ($this->typeOptions == null) {
            $options = [
                self::TYPE_MAIN => __('bizami.warehouse.type.main'),
                self::TYPE_DEPARTMENT => __('bizami.warehouse.type.department'),
            ];
            $this->typeOptions = new Options($options);
        }
        return $this->typeOptions;
    }
}
