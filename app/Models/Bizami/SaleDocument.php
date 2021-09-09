<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Models\Bizami;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Class Product
 * @package App\Models\Bizami
 */
class SaleDocument extends Model
{
    protected $table = 'bizami_sales_document';

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
     * @return false|mixed
     */
    public function getLastDocumentDate()
    {
        $documents = DB::table($this->getTable())->select('date')
            ->orderBy('date', 'desc')
            ->limit(1);

        $saleDocuments = $documents->get();
        foreach ($saleDocuments as $document) {
            return $document->date;
        }
        return false;
    }
}
