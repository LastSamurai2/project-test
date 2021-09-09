<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Dataflows\Profile\Bizami;

use App\Models\Bizami;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class PreCalculate
 * @package App\Dataflows\Profile\Bizami
 */
class PreCalculate extends \App\Models\Dataflows\Profile
{
    /**
     *
     */
    public function execute()
    {
        Bizami\Precalculation::query()->truncate();
        $products = new Bizami\Product();
        $productsTable = $products->getTable();

        DB::table($productsTable)->orderBy('id')
            //->whereIn('sku', ['NI/DUO 032 SKR'])
            ->chunk(1000, function ($products)
        {
            $this->calculateForProducts($products);
        });
    }

    /**
     * @param $productIds
     */
    protected function calculateForProducts($products)
    {
        $calculationDate = $this->getDate();

        $productsData = [];
        $productIds = [];
        $productData = [];

        foreach ($products as $product) {
            $productIds[] = $product->sku;
            $productData[$product->sku] = [
                'provider' => $product->provider,
                'status' => $product->is_active, // is it status ?
                'value' => $product->value,
            ];
        }

        $dateLimitForRotation = clone $calculationDate;
        $dateLimitForRotation = $dateLimitForRotation->sub(90, 'day')->toDateTimeString();

        $from = clone $calculationDate;
        $from = $from->sub(180, 'day')->toDateTimeString();
        $saleDocuments = $this->getSaleDocuments($productIds, $from);

        $dataForMedianaCalulation = [];

        foreach ($saleDocuments as $document) {
            $productId = $document->product_id;

            if (!isset($productsData[$document->product_id])) {
                $productsData[$document->product_id] = [
                    'product_id' => $document->product_id,
                    'provider' => $productData[$document->product_id]['provider'],
                    'rotation_qty' => 0,
                    'rotation_value' => 0,
                    'seasonability' => 0,
                    'has_transactions_in_last_days' => false,
                    'mediana' => 0,
                    'status' => $productData[$document->product_id]['status'],
                    'value' => $productData[$document->product_id]['value'],
                ];
            }

            $qty = $document->qty;
            $documentDate = $document->date;
            $value = $document->value;

            $productsData[$productId]['rotation_qty'] += $qty;
            $productsData[$productId]['rotation_value'] += $value;

            if (!$productsData[$productId]['has_transactions_in_last_days']
                && $dateLimitForRotation <= $documentDate
            ) {
                $productsData[$productId]['has_transactions_in_last_days'] = true;
            }

            if ($value > 0) {   //wyliczona na podstawie dok. WZ bez korekt dla wszystkich magazynów.
                $dataForMedianaCalulation[$productId]['qtys'][] = $qty;
            }
        }

        foreach ($dataForMedianaCalulation as $productId => $data) {
            $sortedQtys = $data['qtys'];
            sort($sortedQtys);
            $documentsQty = count($sortedQtys);

            if ($documentsQty % 2 == 0) {
                $mediana = ($sortedQtys[$documentsQty / 2] + $sortedQtys[$documentsQty / 2 - 1]) / 2;
            } else {
                $mediana = $sortedQtys[floor($documentsQty / 2)];
            }
            $productsData[$productId]['mediana'] = $mediana;
        }

        unset($dataForMedianaCalulation);

        foreach ($productsData as $productId => $data) {
            // rotacja R1 i R2 wynosi 0 gdy brak transakcji w ostatnich 90 dniach.
            if (!$productsData[$productId]['has_transactions_in_last_days']) {
                $productsData[$productId]['rotation_qty'] = 0;
                $productsData[$productId]['rotation_value'] = 0;
            }

            unset($productsData[$productId]['has_transactions_in_last_days']);

            // suma  (szt.) obrotów na transakcjach za ostatnie 180 dni. Podzielona przez 5,9 aby wyrazić to w skali 1 miesiąca.
            $productsData[$productId]['rotation_qty'] = $productsData[$productId]['rotation_qty'] / 5.9;

            //R2.=suma  wartości (zł)obrotów na transakcjach za ostatnie 180 dni. Podzielona przez 5,9 aby wyrazić to w skali 1 miesiąca.
            $productsData[$productId]['rotation_value'] = $productsData[$productId]['rotation_value'] / 5.9;
        }

        $this->calculateSeasonality($productIds, $productsData);

        Bizami\Precalculation::insert($productsData);
    }

    /**
     * @param $productIds
     * @param $productsData
     */
    protected function calculateSeasonality($productIds, &$productsData)
    {
        $algorithmModel = new Bizami\Algorithm();

        // okres A - minimum 10 transakcji;
        // okres B -  minimum 30 transakcji.
        $minADocuments = $algorithmModel->getMinTransactionsQtyForSeasonabilityPeriodA();
        $minBDocuments = $algorithmModel->getMinTransactionsQtyForSeasonabilityPeriodB();

        $calculationDate = $this->getDate();
        $from = clone $calculationDate;
        $from = $from->sub(18, 'months')->toDateTimeString();
        $seasonalibityMiddleDate = clone $calculationDate;
        $seasonalibityMiddleDate = $seasonalibityMiddleDate->sub(12, 'months')->toDateTimeString();
        $to = clone $calculationDate;
        $to = $to->sub(10, 'months')->toDateTimeString();

        $saleDocuments = $this->getSaleDocuments($productIds, $from, $to);
        $seasonalibilityTempArray = [];

        foreach ($saleDocuments as $document) {
            $productId = $document->product_id;
            $documentDate = $document->date;
            $value = $document->value;

            if ($value <= 0) {
                //@todo how calculate returns ??
                continue;
            }

            if (!isset($seasonalibilityTempArray[$productId])) {
                $seasonalibilityTempArray[$productId] = [
                    'a' => 0,
                    'b' => 0,
                    'a_documents_counter' => 0,
                    'b_documents_counter' => 0,
                ];
            }

            $qty = $document->qty;
            $mediana = $productsData[$productId]['mediana'] ?? 0;
            $seasonability = min($qty, $mediana * $algorithmModel->getMedianaMultiplierForSeason());

            if ($documentDate > $seasonalibityMiddleDate) {
                $seasonalibilityTempArray[$productId]['a'] += $seasonability;
                $seasonalibilityTempArray[$productId]['a_documents_counter'] ++;
            } else {
                $seasonalibilityTempArray[$productId]['b'] += $seasonability;
                $seasonalibilityTempArray[$productId]['b_documents_counter'] ++;
            }
        }

        foreach ($seasonalibilityTempArray as $productId => $seasonablityData) {
            if ($seasonalibilityTempArray[$productId]['a_documents_counter'] < $minADocuments) {
                continue;
            }

            if ($seasonalibilityTempArray[$productId]['b_documents_counter'] < $minBDocuments) {
                continue;
            }

            $a = $seasonablityData['a'];
            $b = $seasonablityData['b'];

            if ($b > 0) {  /// why its sometimes 0 ? what to do ?
                $productsData[$productId]['seasonability'] = ($a - ($b / 3)) / ($b / 3);
            }
        }
    }

    /**
     * @return Carbon
     */
    protected function getDate()
    {
        return new Carbon();
    }

    protected function getSaleDocuments($productIds, $from = false, $to = false)
    {
        $documentsModel = new Bizami\SaleDocument();
        $documents = DB::table($documentsModel->getTable())->whereIn('product_id', $productIds);

        if ($from) {
            $documents->where('date', '>', $from);
        }

        if ($to) {
            $documents->where('date', '<=', $to);
        }

        return $documents->get();
    }
}
