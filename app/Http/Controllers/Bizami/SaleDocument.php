<?php

namespace App\Http\Controllers\Bizami;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class SaleDocument
 * @package App\Http\Controllers\Bizami
 */
class SaleDocument extends Controller
{

    public function index(Request $request)
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $query->where('document_id', 'LIKE', "%{$value}%")
                    ->orWhere('product_id', 'LIKE', "%{$value}%");
            });
        });

        $documents = QueryBuilder::for(\App\Models\Bizami\SaleDocument::class)
            ->defaultSort('date')
            ->allowedSorts(['date', 'qty', 'value'])
            ->allowedFilters(['document_id', 'product_id', 'type', 'warehouse_id', 'is_investment', $globalSearch])
            ->paginate(10)
            ->withQueryString();


        return Inertia::render('Bizami/SaleDocument', [
            'sale_documents' => $documents,
        ])->table(function (InertiaTable $table) {
            $table->addSearchRows([
                'warehouse_id' => 'Warehouse ID',
                'is_investment' => 'Is Investment',
                'type' => 'Type',
            ])->addColumns([
            ]);
        });
    }
}
