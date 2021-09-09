<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Models\Bizami;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * Class Product
 * @package App\Models\Bizami
 */
class Product extends Model
{
    protected $table = 'bizami_product';

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
