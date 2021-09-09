<?php

namespace App\Http\Controllers\Bizami;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class Warehouse
 * @package App\Http\Controllers\Bizami
 */
class Warehouse extends Controller
{

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $query->where('warehouse_id', 'LIKE', "%{$value}%");
            });
        });

        $warehouses = QueryBuilder::for(\App\Models\Bizami\Warehouse::class)
            ->defaultSort('id')
            ->allowedSorts(['warehouse_id'])
            ->allowedFilters(['warehouse_id', 'type', $globalSearch])
            ->paginate(10)
            ->withQueryString();


        return Inertia::render('Bizami/Warehouse/List', [
            'warehouses' => $warehouses,
            'columns' => [
                'type' => [
                    'options' => $this->getTypeOptions()->getOptions(),
                ],
            ],
        ])->table(function (InertiaTable $table) {
            $table->addFilter('type', __('bizami.warehouse.field.type'), $this->getTypeOptions()->getOptionsArray());
        });
    }

    /**
     * @param null $id
     * @return \Inertia\Response
     */
    public function edit($id)
    {
        $warehouses = \App\Models\Bizami\Warehouse::all();
        $warehouse = $warehouses->find($id);

        if (!$warehouse) {
            return Redirect::route('bizami.warehouse')->withFlash(['error' => __('bizami.warehouse.flash.not-found')]);
        }

        return Inertia::render('Bizami/Warehouse/Edit', [
            'warehouse' => $warehouse,
            'fields' => [
                'type' => [
                    'options' => $this->getTypeOptions()->getOptions(),
                ],
            ],
            'buttons' => [
                array(
                    "label" => "button.back",
                    "url" => route('bizami.warehouse')
                )
            ]
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     */
    public function update(Request $request, $id)
    {
        $warehouses = \App\Models\Bizami\Warehouse::all();
        $warehouse = $warehouses->find($id);
        $this->setWarehouseData($warehouse, $request);
        $warehouse->save();
        return Redirect::route('bizami.warehouse')->withFlash(['success' => __('bizami.warehouse.flash.updated')]);
    }

    /**
     * @return mixed
     */
    protected function getTypeOptions()
    {
        $warehouse = new \App\Models\Bizami\Warehouse();
        $options = $warehouse->getTypeOptions();
        return $options;
    }

    /**
     * @param $algorithm
     * @param $request
     * @return $this
     */
    protected function setWarehouseData($warehouse, $request)
    {
        $columns = Schema::getColumnListing($warehouse->getTable());
        $notEditableFields = ['id', 'warehouse_id'];
        foreach($columns as $key)
        {
            if (in_array($key, $notEditableFields)) {
                continue;
            }
            if (isset($request->$key)) {
                $warehouse->$key = $request->$key;
            }
        }

        return $this;
    }
}
