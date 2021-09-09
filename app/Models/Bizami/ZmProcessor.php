<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Models\Bizami;

use App\Models\Bizami;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class ZmProcessor
 * @package App\Models\Bizami
 */
class ZmProcessor
{
    protected $pageSize = 2000;
    /**
     * @var
     */
    protected $algorithm;
    /**
     * @var
     */
    protected $provider;
    /**
     * @var
     */
    protected $productsCalculatedData;
    /**
     * @var
     */
    protected $productIds;
    /**
     * @var
     */
    protected $iterationNumber;
    /**
     * @var int
     */
    protected $totalIterations = 1;
    /**
     * @var
     */
    protected $productQtys;
    /**
     * @var
     */
    protected $saleDocuments;
    /**
     * @var array
     */
    protected $outputModels = [];

    /**
     * @param $outputModel
     * @return $this
     */
    public function addOutputModel($outputModel)
    {
        $this->outputModels[] = $outputModel;
        return $this;
    }

    /**
     * @param $provider
     * @return $this
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
        return $this;
    }

    /**
     * @param $productIds
     * @return $this
     */
    public function setProductIds($productIds)
    {
        if (!is_array($productIds)) {
            $productIds = [$productIds];
        }

        $this->productIds = $productIds;
        return $this;
    }

    /**
     * @param $iterationNumber
     * @return $this
     */
    public function setIterationNumber($iterationNumber)
    {
        $this->iterationNumber = $iterationNumber;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalIterations()
    {
        return $this->totalIterations;
    }

    /**
     * @param $algorithmId
     * @return $this
     */
    public function setAlgorithm($algorithmId)
    {
        $algorithm = Algorithm::all();
        $this->algorithm = $algorithm->find($algorithmId);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAlgorithm()
    {
        return $this->algorithm;
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $this->log(Carbon::now()->toDateTimeString() . ' - first validation');
        $this->productsFirstValidation();
        $this->log(' - validated products: ' . count($this->productsCalculatedData));
        $this->log(Carbon::now()->toDateTimeString() . ' - calculate zm');
        $this->caluclateZm();
        $this->log(Carbon::now()->toDateTimeString() . ' - corrections');
        $this->doCorrections();
        $this->log(Carbon::now()->toDateTimeString() . ' - done');

        return $this->productsCalculatedData;
    }

    protected function getProvider()
    {
        return $this->provider;
    }

    /**
     * @return $this
     */
    protected function productsFirstValidation()
    {
        $this->productsCalculatedData = [];
        $provider = $this->getProvider();

        $precalculationModel = new Bizami\Precalculation();
        $productCalculations = DB::table($precalculationModel->getTable());

        if ($provider) {
            $productCalculations->where('provider', $provider);
        }

        if ($this->productIds !== null) {
            $productCalculations->whereIn('product_id', $this->productIds);
        } else {
            $this->productIds = [];
        }

        if ($this->iterationNumber !== null) {
            $productCalculationsClone = clone($productCalculations);
            $totalProductCalculations = $productCalculationsClone->count();
            $productCalculations->limit($this->pageSize)->offset($this->pageSize * $this->iterationNumber);
            $this->totalIterations = max(ceil($totalProductCalculations / $this->pageSize), 1);
        }

        $productCalculations = $productCalculations->get();

        foreach ($productCalculations as $productPreCalculation) {
            $productId = $productPreCalculation->product_id;
            $this->productIds[$productId] = $productId;
        }

        $wsSez = $this->algorithm->getSeasonabilityMultiplier();
        $tMin = $this->algorithm->getMinCycle();

        foreach ($productCalculations as $productPreCalculation) {
            $productId = $productPreCalculation->product_id;
            $seasonability = $productPreCalculation->seasonability;
            $r1 = $productPreCalculation->rotation_qty;
            $reserved = $this->getReservedQty($productId);
            $inStockQty = $this->getInStockQty($productId);
            $orderedQty = $this->getOrderedQty($productId);

            $zm = $reserved + ($r1 / 30) * (1 + $seasonability * $wsSez / 100) * $tMin - $inStockQty - $orderedQty;

            if ($zm > 0) {
                $this->productsCalculatedData[$productId] = [
                    'index' => '',
                    'product_id' => $productId,
                    'grp_mat' => '',
                    'first_zm' => $zm,
                    'in_stock_qty' => $inStockQty,
                    'ordered_qty' => $orderedQty,
                    'reserved_qty' => $reserved,
                    'mediana' => $productPreCalculation->mediana,
                    'rotation_qty' => $productPreCalculation->rotation_qty,
                    'rotation_value' => $productPreCalculation->rotation_value,
                    'seasonability' => $productPreCalculation->seasonability,
                    'description' => '', // @todo
                    'status' => $productPreCalculation->status,
                    'cz'=> $productPreCalculation->value,
                    'wz' => 0,
                    'pr_wart_med' => $productPreCalculation->mediana * 2,
                ];
            }
        }

        return $this;
    }

    /**
     * @param $productId
     * @return int|mixed
     */
    protected function getInStockQty($productId)
    {
        if ($this->productQtys === null) {
            $this->productQtys = [];

            $warehouseStatesModel = new Bizami\WarehouseState();
            $warehouseStates = DB::table($warehouseStatesModel->getTable())
                ->whereIn('product_id', $this->productIds)
                ->get();

            foreach ($warehouseStates as $warehouseState) {
                $warehouseProductId = $warehouseState->product_id;
                if (!isset($this->productQtys[$warehouseProductId])) {
                    $this->productQtys[$warehouseProductId] = [
                        'in_stock' => 0,
                        'ordered' => 0,
                        'reserved' => 0,
                    ];
                }
                $this->productQtys[$warehouseProductId]['in_stock'] += $warehouseState->qty;
                $this->productQtys[$warehouseProductId]['ordered'] += $warehouseState->qty_ordered;
                $this->productQtys[$warehouseProductId]['reserved'] += $warehouseState->qty_reserved;
            }
        }

        if (isset($this->productQtys[$productId])) {
            return $this->productQtys[$productId]['in_stock'];
        } else {
            return 0;
        }
    }

    /**
     * @param $productId
     * @return mixed
     */
    public function getOrderedQty($productId)
    {
        $this->getInStockQty($productId); // just to be sure that productTys is filled
        if (isset($this->productQtys[$productId])) {
            return $this->productQtys[$productId]['ordered'];
        } else {
            return 0;
        }
    }

    /**
     * @param $productId
     * @return int|mixed
     */
    protected function getReservedQty($productId)
    {
        $this->getInStockQty($productId); // just to be sure that productTys is filled
        if (isset($this->productQtys[$productId])) {
            return $this->productQtys[$productId]['reserved'];
        } else {
            return 0;
        }
    }

    /**
     *
     */
    protected function caluclateZm()
    {
        foreach ($this->productsCalculatedData as $productId => $productCalculatedData) {
            $t = $this->algorithm->getCycle($productCalculatedData['rotation_value']);
            $this->productsCalculatedData[$productId]['t'] = $t;
            $saleDocuments = $this->getSaleDocuments($productId);
            $fixPicsFlag = 0; // czy korygujemy piki

            foreach ($saleDocuments as $saleDocument) {
                if ($saleDocument->value <= 0) {
                    $this->productsCalculatedData[$productId]['wz'] --;
                    continue;
                } else {
                    $this->productsCalculatedData[$productId]['wz'] ++;
                }

                if ($saleDocument->qty > $productCalculatedData['mediana'] * $this->algorithm->getMedianaMultiplierForZmm()) {
                    $fixPicsFlag ++;
                }
            }

            if ($fixPicsFlag >= $this->algorithm->getPicksThreshold()) {
                $correctPicsFlag = false;
            } else {
                $correctPicsFlag = true;
            }

            $this->productsCalculatedData[$productId]['correct_picks_flag'] = $correctPicsFlag;

            $totalQty = 0;
            foreach ($saleDocuments as $saleDocument) {
                $qty = $saleDocument->qty;
                if ($correctPicsFlag) {
                    if ($qty >= $productCalculatedData['mediana'] * $this->algorithm->getMedianaMultiplierForPicks()) {
                        $qty = $productCalculatedData['mediana'] * $this->algorithm->getMedianaMultiplierForPicks();
                    }
                }

                $totalQty += $qty;
            }

            $r1p = round($totalQty / 5.9, 2);
            $this->productsCalculatedData[$productId]['rotation_qty_p'] = $r1p;
            $this->productsCalculatedData[$productId]['zm'] = $this->getZmForProductData($this->productsCalculatedData[$productId]);
        }
    }

    /**
     * @param $productCalculatedData
     * @return float|int|mixed
     */
    protected function getZmForProductData($productCalculatedData)
    {
        $reservedQty =  $productCalculatedData['reserved_qty'];
        $inStockQty = $productCalculatedData['in_stock_qty'];
        $orderedQty = $productCalculatedData['ordered_qty'];

        $r1p = $productCalculatedData['rotation_qty_p'];
        $seasonability = $productCalculatedData['seasonability'];
        $wsSez = $this->algorithm->getSeasonabilityMultiplier();
        $t = $productCalculatedData['t'];
        $tDost = $this->getTDost();

        $zm = $reservedQty + ($r1p / 30) * (1 + $seasonability * $wsSez / 100) * ($t + $tDost) - $inStockQty - $orderedQty;

        if ($zm < 0) {
            $zm = 0;
        }

        return ceil($zm);
    }

    /**
     *
     */
    protected function doCorrections()
    {
        foreach ($this->productsCalculatedData as $productId => $productCalculatedData) {
            $zm = $productCalculatedData['zm'];
            $reserved =  $productCalculatedData['reserved_qty'];
            $inStockQty = $productCalculatedData['in_stock_qty'];
            $orderedQty = $productCalculatedData['ordered_qty'];
            $tDost = $this->getTDost();

            $zmm = $zm;
            $this->productsCalculatedData[$productId]['zm_before_correction'] = $zm;
            if ($productCalculatedData['status']  /// Gdy Status =0. to Nie liczymy ZMM od mediany.
                && $zm > 0  //kolejność obliczeń - gdy ZM od rotacji <=0 to nie liczymy ZMM od mediany.
            ) {
                $zmm =  $productCalculatedData['mediana'] * $this->algorithm->getMedianaMultiplierForZmm() + $reserved - $inStockQty - $orderedQty - $tDost;
            }

            //if ($cenaproduktu < progZ tabeli i status = 1) {
            //    $zmm = mediana * mnoznik z tabeli;
            //}

            if ($productCalculatedData['zm'] < $zmm) {
                $this->productsCalculatedData[$productId]['zm'] = $zmm;
            }
            $this->productsCalculatedData[$productId]['zmm'] = $zmm;

            // @todo not sure about this :
            // gdy ZM =< 0,25 *(IM-ZS- ZD/Trans) to ZM=0
            $wd1 = ($this->algorithm->getWD1AdditionalMinimumCondition() / 100) * (($inStockQty + $orderedQty) - $reserved);
            $value = $this->productsCalculatedData[$productId]['cz'];

            if ($zm <= $wd1) {
                $zm = 0;
                $this->productsCalculatedData[$productId]['zm'] = $zm;
                $this->productsCalculatedData[$productId]['wd1'] = $wd1;
            } else if ($zm * $value < $this->algorithm->getWD2ReactionValueThreshold()) {
                // WD2; gdy wartość pozycji w zł. dla ZM <(np.. 200,-) to T=T*(1+0,7)
                $t = $productCalculatedData['t'] * (1 + $this->algorithm->getWD2CycleExtended());
                $newCalculatedData = $this->productsCalculatedData[$productId];
                $newCalculatedData['t'] = $t;
                $zm = $this->getZmForProductData($newCalculatedData);
                $this->productsCalculatedData[$productId]['wd2'] = $zm;
            }

            if ($zm > 0) {
                $productValue = $this->productsCalculatedData[$productId]['cz'];
                $this->productsCalculatedData[$productId]['zm'] = $zm;
                $this->productsCalculatedData[$productId]['correction'] = number_format(($zm * $productValue) - ($this->productsCalculatedData[$productId]['zm_before_correction'] * $productValue), 2);
                $this->productsCalculatedData[$productId]['color'] = '';
                $this->productsCalculatedData[$productId]['value'] = number_format($zm * $this->productsCalculatedData[$productId]['cz'], 2);

                /**
                 * @todo colors
                 */
                if ($zm < 2) {
                    $this->productsCalculatedData[$productId]['color'] = '#78acff';
                }
            } else {
                unset($this->productsCalculatedData[$productId]);
            }
        }
    }

    /**
     * @param $productId
     * @return array|mixed
     */
    protected function getSaleDocuments($productId)
    {
        if ($this->saleDocuments === null) {
            $from = Carbon::today()->sub(180, 'days')->toDateTimeString();

            $documentsModel = new Bizami\SaleDocument();

            DB::table($documentsModel->getTable())
                ->orderBy('id')
                ->where('date', '>', $from)
                ->chunk(10000, function ($saleDocuments)
                {
                    foreach ($saleDocuments as $saleDocument) {
                        if (isset($this->productIds[$saleDocument->product_id])) {
                            $this->saleDocuments[$saleDocument->product_id][] = $saleDocument;
                        }
                    }
                });
        }

        if (isset($this->saleDocuments[$productId])) {
            return $this->saleDocuments[$productId];
        } else {
            return [];
        }
    }

    /**
     * @return int
     */
    protected function getTDost()
    {
        return 0; // @todo for now always 0
    }

    /**
     * @param $message
     */
    protected function log($message)
    {
        foreach ($this->outputModels as $outputModel) {
            $outputModel->output($message);
        }
    }
}

