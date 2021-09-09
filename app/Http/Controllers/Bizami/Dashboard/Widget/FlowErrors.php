<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Http\Controllers\Bizami\Dashboard\Widget;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Dataflows;

/**
 * Class SalesWidget
 * @package App\Http\Controllers\Bizami
 */
class FlowErrors extends Controller
{
    /**
     * @return false|string
     */
    public function index()
    {

        $logModel = new Dataflows\Report\Log();
        $logLevel = $logModel::LOG_LEVEL_CRITICAL;
        $lastDate = $logModel->getLastLogDate($logLevel);
        $logCollection = [];

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

            $logCollection = DB::table($logModel->getTable())
                ->where([
                    ['created_at', '>=', $from->toDateString()],
                    ['level', '=', $logLevel]
                ])
                ->orderByDesc('created_at')
                ->paginate(10);
        }

        return json_encode($logCollection);
    }
}