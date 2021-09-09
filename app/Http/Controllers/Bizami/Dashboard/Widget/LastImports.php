<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Http\Controllers\Bizami\Dashboard\Widget;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Dataflows;

/**
 * Class SalesWidget
 * @package App\Http\Controllers\Bizami
 */
class LastImports extends Controller
{
    /**
     * @return false|string
     */
    public function index()
   {
       $reportsModel = new Dataflows\Report();
       $reports = DB::table($reportsModel->getTable())
           ->paginate(6);

       return json_encode($reports);
   }

}