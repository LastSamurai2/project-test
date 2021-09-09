<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Http\Controllers\Bizami\Simulation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bizami;

/**
 * Class TableData
 * @package App\Http\Controllers\Bizami\Simulation
 */
class TableData extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $id = null, $viewType = null)
    {
        $simulation = Bizami\Simulation::all()->find($id);
        if ($simulation) {
            $result = json_decode($simulation->content, true);
        }

        $isAnalyticsView = false;
        if ($viewType == 'analytics') {
            $isAnalyticsView = true;
        }

        $columns = [];

        //  Indeks – nasz wewnętrzny identyfikator
        //$columns[] = [
        //    'label' => 'index',
        //    'code' => 'index',
        //];

        //  Opis – nazwa pozycji
        //$columns[] = [
        //        'label' => 'Opis',
        //        'code' => 'description',
        //];

        // NrKat – numer katalogowy
        $columns[] = [
            'label' => 'NrKat',
            'code' => 'product_id',
        ];

        // Grp_Mat – grupa materiałowa
        //$columns[] = [
        //    'label' => 'Grp_Mat',
        //    'code' => 'grp_mat',
        //];

        $columns[] = [
            'label' => 'ZS',
            'code' => 'reserved_qty',
            'color' => "#32a840",
            'countable' => true,
        ];

        // LimitZS – 35% z ilośći ZS
        // jest w opisie pol, ale nie ma na screenie
        //$columns[] = [
        //    'label' => 'LimitZS',
        //    'code' => 'limit_zs',
        //    'color' => "#32a840",
        //];

        // ilość zamówiona do dostawcy na magazynie docelowym
        $columns[] = [
            'label' => 'ZD',
            'code' => 'ordered_qty',
            'color' => "#32a840",
        ];

        //  ilość aktualnie znajdującasię na magazynie docelowym
        $columns[] = [
            'label' => 'IM',
            'code' => 'in_stock_qty',
            'color' => '#a86b32',
        ];

        // ilość na magazynie GD Handlowy
        //$columns[] = [
        //    'label' => 'IG',
        //    'code' => 'ig',
        //];

        // ilośćznajdującasię na magazynach w oddziałach (suma)
        //$columns[] = [
        //    'label' => 'IO',
        //    'code' => 'io',
        //];

        // Ilośćznajdującasię na magazynie Logis
        //$columns[] = [
        //    'label' => 'IL',
        //    'code' => 'il',
        //];

        // TRANS; BRAK - inne magazyny, stany na tych magazynach mogąbyćużyte w algorytmach lub informacyjnie
        //$columns[] = [
        //    'label' => 'TRANS',
        //    'code' => 'trans_qty',
        //    'color' => '#a86b32',
        //];

        // TRANS; BRAK - inne magazyny, stany na tych magazynach mogąbyćużyte w algorytmach lub informacyjnie
        //$columns[] = [
        //    'label' => 'Braki',
        //    'code' => 'braki',
        //    'color' => '#feffb0',
        //];

        // WZ- Ilość wydanych sztuk z uwzględnieniem zwrotów w okresie 6 miesięcy
        $columns[] = [
            'label' => 'WZ',
            'code' => 'wz',
        ];

        if ($isAnalyticsView) {
            // R1PS – Suma z ilości rotacji skorygowana
            $columns[] = [
                'label' => 'R1PS',
                'code' => 'rotation_qty_p',
                'color' => '#a1a1a1',
            ];

            // R2PS - Suma z wartości rotacji skorygowana
            $columns[] = [
                'label' => 'R2PS',
                'code' => 'r2ps',
                'color' => '#a1a1a1',
            ];
        }

        // % - wskaźniksezonowości
        $columns[] = [
            'label' => 'Sez %',
            'code' => 'seasonability',
        ];

        //  ST – Status produktu na magazynie docelowym
        $columns[] = [
            'label' => 'Status',
            'code' => 'status',
            'color' => '#a86b32',
        ];

        // T – ilość dni długości cyklu
        $columns[] = [
            'label' => 'T',
            'code' => 't',
        ];

        // ZM – wynik obliczeń algorytmu
        $columns[] = [
            'label' => 'ZM',
            'code' => 'zm',
            'color' => '#a1a1a1',
            'editable' => true,
            'countable' => true,
        ];
        if ($isAnalyticsView) {

            // ZM zaok – wynik obliczeń algorytmu zaokrąglony do opakowań
            //$columns[] = [
            //    'label' => 'ZM zaok',
            //    'code' => 'zm_zaok',
            //];

            // OPAK/Opakowanie – ilość sztuk w opakowaniu
            //$columns[] = [
            //    'label' => 'Opakowanie',
            //    'code' => 'opak',
            //    'color' => '#a1a1a1',
            //];

            // ZMR – wyliczona wartość pozycji zamówienia na bazie rotacji
            $columns[] = [
                'label' => 'ZMR',
                'code' => 'zm_before_correction',
            ];

            // ZMM – wyliczona wartość pozycji zamówienia na bazie mediany
            $columns[] = [
                'label' => 'ZMM',
                'code' => 'zmm',
            ];

            //  mediana pozycji pomnożona przez 2
            $columns[] = [
                'label' => 'PR_WART_MED',
                'code' => 'pr_wart_med',
            ];

            //  warunek dodatkowy minimum, limit zerowania pozycji
            $columns[] = [
                'label' => 'WD1',
                'code' => 'wd1',
                'color' => '#feffb0',
            ];

            //  warunek dodatkowy maksimum
            $columns[] = [
                'label' => 'WD2',
                'code' => 'wd2',
                'color' => '#feffb0',
            ];
        }

        // cena zakupu
        $columns[] = [
            'label' => 'CZ',
            'code' => 'cz',
        ];

        // wartość pozycji
        $columns[] = [
            'label' => 'Wartość',
            'code' => 'value',
            'countable' => true,
        ];

        if ($isAnalyticsView) {
            // różnica wartości końcowej od wyliczonej
            $columns[] = [
                'label' => 'Korekta Wartości',
                'code' => 'correction',
                'color' => '#feffb0',
                'countable' => true,
            ];
        }

        // Ilość rotacji pozycji
        $columns[] = [
            'label' => 'R1',
            'code' => 'rotation_qty',
            'color' => '#a1a1a1',
        ];

        // Wartość rotacji pozycji
        $columns[] = [
            'label' => 'R2',
            'code' => 'rotation_value',
            'color' => '#a1a1a1',
        ];

        // Waga
        //$columns[] = [
        //    'label' => 'WAGA',
        //    'code' => 'weiht',
        //    'countable' => true,
        //];

        // Objętość
        //$columns[] = [
        //    'label' => 'OBJECTSC',
        //    'code' => 'obj',
        //    'countable' => true,
        //];

        //  Suma  rotacji
        $columns[] = [
            'label' => 'Suma Rotacji',
            'code' => 'total',
            'color' => '#a1a1a1',
        ];

        return response()->json(
            [
                'data' => array_values($result),
                'columns' => $columns,
            ]
        );
    }

}
