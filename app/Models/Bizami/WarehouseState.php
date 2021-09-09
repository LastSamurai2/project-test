<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Models\Bizami;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * Class WarehouseState
 * @package App\Models\Bizami
 */
class WarehouseState extends Model
{
    protected $table = 'bizami_warehouse_state';

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
}
