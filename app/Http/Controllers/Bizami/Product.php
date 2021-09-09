<?php

namespace App\Http\Controllers\Bizami;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class Schedule
 * @package App\Http\Controllers\Bizami
 */
class Product extends Controller
{

    public function index(Request $request)
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $query->where('name', 'LIKE', "%{$value}%")
                    ->orWhere('sku', 'LIKE', "%{$value}%");
            });
        });

        $products = QueryBuilder::for(\App\Models\Bizami\Product::class)
            ->defaultSort('id')
            ->allowedSorts(['sku', 'name'])
            ->allowedFilters(['name', 'sku', 'provider', $globalSearch])
            ->paginate(10)
            ->withQueryString();


        return Inertia::render('Bizami/Product', [
            'products' => $products,
        ])->table(function (InertiaTable $table) {
            $table->addSearchRows([
                'provider' => __('bizami.provider'),
                'name' => __('bizami.name'),
                'sku' => __('bizami.sku')
            ])->addColumns([
                'provider' => __('bizami.provider'),
                'name' => __('bizami.name'),
                'sku' => __('bizami.sku'),
                'catalog_group' => __('bizami.catalog_group'),
                'producent' => __('bizami.producent'),
                'gross_weight' => __('bizami.gross.weight'),
                'gross_volume' => __('bizami.gross.volume'),
                'category' => __('bizami.category'),
                'is_active' => __('bizami.status')
            ]);
        });
    }
}
