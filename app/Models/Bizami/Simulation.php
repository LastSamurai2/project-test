<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Models\Bizami;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Simulation
 * @package App\Models\Bizami
 */
class Simulation extends Model
{
    const TYPE_SUPPLIER = 'supplier';
    const TYPE_DEPARTMENT = 'department';

    protected $table = 'bizami_simulation';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s'
    ];
}
