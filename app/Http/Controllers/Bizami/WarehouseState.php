<?php

namespace App\Http\Controllers\Bizami;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class WarehouseState
 * @package App\Http\Controllers\Bizami
 */
class WarehouseState extends Controller
{

    public function index(Request $request)
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $query->where('product_id', 'LIKE', "%{$value}%");
            });
        });

        $warehouseStates = QueryBuilder::for(\App\Models\Bizami\WarehouseState::class)
            ->defaultSort('id')
            ->allowedSorts(['qty', 'qty_reserved', 'qty_ordered', 'norm'])
            ->allowedFilters(['warehouse_id', 'product_id', 'status', $globalSearch])
            ->paginate(10)
            ->withQueryString();


        return Inertia::render('Bizami/WarehouseState', [
            'warehouse_states' => $warehouseStates,
        ])->table(function (InertiaTable $table) {
            $table->addFilter('status', __('bizami.status'), [
                'disabled' => __('disabled'),
                'enabled' => __('enabled')
            ])->addSearchRows([
                'warehouse_id' => 'Warehouse ID',
                'status' => 'Status',
            ])->addColumns([
            ]);
        });
    }
}
