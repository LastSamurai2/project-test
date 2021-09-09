<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Models\Bizami;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Parameter
 * @package App\Models\Bizami
 */
class Precalculation extends Model
{
    protected $table = 'bizami_precalculation';

    /**
     * @return false
     */
    public function usesTimestamps()
    {
        return false;
    }
}
