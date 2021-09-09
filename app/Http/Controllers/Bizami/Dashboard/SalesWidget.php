<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Http\Controllers\Bizami\Dashboard;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Bizami;

/**
 * Class SalesWidget
 * @package App\Http\Controllers\Bizami
 */
class SalesWidget extends Controller
{
    /**
     * @return false|string
     */
    public function index()
    {
        $dataObj = (object)[];
        $dataObj->labels = [];
        $dataObj->data = [];

        $documentsModel = new Bizami\SaleDocument();
        $lastDate = $documentsModel->getLastDocumentDate();

        if ($lastDate) {
            $days = 7;
            $from = new Carbon($lastDate);
            $from = $from->sub($days - 1, 'days');

            $result = [];
            $date = clone $from;
            for ($i = 0; $i < $days; $i ++) {
                $result[$date->toDateString()] = 0;
                $date->add(1, 'day');
            }

            $documents = DB::table($documentsModel->getTable())->select(DB::raw('count(*) as sales_count, date'))->groupBy('date');
            $documents->where('date', '>=', $from->toDateString());
            $saleDocuments = $documents->get();

            foreach ($saleDocuments as $saleDocument) {
                if (isset($result[$saleDocument->date])) {
                    $result[$saleDocument->date] = $saleDocument->sales_count;
                }
            }

            foreach ($result as $date => $counter) {
                $dataObj->labels[] = $date;
                $dataObj->data[] = $counter;
            }
        }

        return json_encode($dataObj);
    }
}