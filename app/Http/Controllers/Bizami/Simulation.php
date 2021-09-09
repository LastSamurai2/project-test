<?php

namespace App\Http\Controllers\Bizami;

use App\Http\Controllers\Controller;
use App\Models\Bizami\ZmProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class Simulation
 * @package App\Http\Controllers\Bizami
 */
class Simulation extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $query->where('algorithm_name', 'LIKE', "%{$value}%")->orWhere('supplier', 'LIKE', "%{$value}%");
            });
        });

        $simulationCollections = QueryBuilder::for(\App\Models\Bizami\Simulation::class)
            ->defaultSort('id')
            ->allowedFilters([$globalSearch]);

        $simulationCollections->getQuery()
            ->join('users', 'users.id', '=', 'bizami_simulation.user_id')
            ->select([
                    'bizami_simulation.*',
                    'users.name AS user_name',
                ]
            );

        $simulations = $simulationCollections
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Bizami/Simulation/List', [
            'simulations' => $simulations,
            'buttons' => [
                array(
                    "label" => "bizami.button.create-simulation",
                    "url" => route('bizami.simulation.new')
                )
            ]
        ])->table(function (InertiaTable $table) {
        });
    }

    /**
     * @param Request $request
     * @return \Inertia\Response
     */
    public function new(Request $request)
    {
        return Inertia::render('Bizami/Simulation/New', [
            'fields' => [
                'algorithm' => [
                    'options' => $this->getAlgorithmOptions(),
                ],
                'provider' => [
                    'options' => $this->getProviderOptions(),
                ],
                'warehouse' => [
                    'options' => $this->getWarehouseOptions(),
                ],
            ],
            'buttons' => [
                array(
                    "label" => __("button.back"),
                    "url" => route('bizami.simulations')
                )
            ]
        ]);
    }

    /**
     *
     */
    public function create(\Illuminate\Http\Request $request, $id = null)
    {
        $zmProcessr = new ZmProcessor();
        $content = [];
        $currentIteration = 0;

        if (!$id) {
            \Illuminate\Support\Facades\Request::validate([
                'algorithm' => ['required'],
                'provider' => ['required'],
                'warehouse' => ['required']
            ]);

            $algorithmId = $request->get('algorithm');
            $provider = $request->get('provider');

            $zmProcessr->setProvider($provider);
            $zmProcessr->setAlgorithm($algorithmId);
            $algorithm = $zmProcessr->getAlgorithm();
            $simulation = new \App\Models\Bizami\Simulation();
            $simulation->user_id = auth()->user()->id;
            $simulation->supplier = $provider;
            $simulation->type = \App\Models\Bizami\Simulation::TYPE_SUPPLIER;
            $simulation->algorithm_name = $algorithm->name;
            $simulation->algorithm_id = $algorithm->id;
            $simulation->algorithm_settings = json_encode($algorithm->getSettings());
        } else {
            $simulations = \App\Models\Bizami\Simulation::all();
            $simulation = $simulations->find($id);
            $currentIteration = $simulation->processed_iterations;
            $zmProcessr->setProvider($simulation->supplier);
            $zmProcessr->setAlgorithm($simulation->algorithm_id);
            $content = json_decode($simulation->content, true);
        }

        $zmProcessr->setIterationNumber($currentIteration);
        $result = $zmProcessr->execute();

        $simulation->total_iterations = $zmProcessr->getTotalIterations();
        $progress = round(($currentIteration + 1) / $simulation->total_iterations * 100);
        $result = array_merge($content, $result);
        $content = json_encode($result);

        $simulation->orignal_content = $content;
        $simulation->content = $content;
        $simulation->processed_iterations = $currentIteration + 1;
        $simulation->calculation_progress = $progress;
        $simulation->save();

        return response()->json(
            [
                'id' => $simulation->id,
                'is_last_iteration' => $simulation->total_iterations <= $currentIteration + 1,
                'progress' => $progress,
            ]
        );
    }

    /**
     * @param Request $request
     */
    public function view(\Illuminate\Http\Request $request, $id, $viewType = '')
    {
        $simulations = \App\Models\Bizami\Simulation::all();
        $simulation = $simulations->find($id);

        if ($simulation) {
            return Inertia::render('Bizami/Simulation/View',
                [
                    'simulation' => $simulation,
                    'viewType' => $viewType,
                ]
            );
        } else {
            return Redirect::route('bizami.simulations')->withFlash(['error' => __('bizami.simulation.flash.not-found')]);
        }
    }

    /**
     * @param Request $request
     */
    public function update(\Illuminate\Http\Request $request, $id, $viewType = '')
    {
        $errorMsg = false;
        $simulations = \App\Models\Bizami\Simulation::all();
        $simulation = $simulations->find($id);
        $editableFields = ['zm'];

        if ($simulation) {
            $newContent = [];
            $postData = $request->post();
            foreach ($postData as $row) {
                $newContent[$row['product_id']] = $row;
            }

            $content = json_decode($simulation->content, true);
            foreach ($content as $row) {
                if (isset($newContent[$row['product_id']])) {
                    foreach ($editableFields as $fieldCode) {
                        if (isset($newContent[$row['product_id']][$fieldCode])) {
                            $vewValue = $newContent[$row['product_id']][$fieldCode];
                            $content[$row['product_id']][$fieldCode] = $vewValue;
                        }
                    }
                }
            }

            $simulation->content = json_encode($content);
            try {
                $simulation->save();
            } catch (\Exception $e) {
                $errorMsg = $e->getMessage();
            }
        } else {
            $errorMsg =  __('bizami.simulation.flash.not-found');
        }

        $redirect = Redirect::route('bizami.simulation.view',
            [
                'id' => $id,
                'view' => $viewType,
            ]
        );

        if ($errorMsg) {
            return $redirect->withFlash(['error' => $errorMsg]);
        } else {
            return $redirect->withFlash(['success' => __('bizami.simulation.flash.updated')]);
        }
    }

    /**
     * @return array
     */
    protected function getAlgorithmOptions()
    {
        $options = [];

        $algorithms = \App\Models\Bizami\Algorithm::all();

        foreach ($algorithms as $algorithm) {
            $options[] = [
                'code' => $algorithm->id,
                'label' => $algorithm->name,
                'type' => $algorithm->type,
            ];
        }

        return $options;
    }

    /**
     * @return array
     */
    protected function getWarehouseOptions()
    {
        $options = [];

        $warehouseAlgorithmTypesMap = [
            \App\Models\Bizami\Warehouse::TYPE_MAIN => \App\Models\Bizami\Algorithm::TYPE_SUPPLIER,
            \App\Models\Bizami\Warehouse::TYPE_DEPARTMENT => \App\Models\Bizami\Algorithm::TYPE_DEPARTMENT
        ];

        $allowedWarehouseTypes = array_keys($warehouseAlgorithmTypesMap);
        $warehouses = \App\Models\Bizami\Warehouse::all()
            ->whereIn('type', $allowedWarehouseTypes);

        foreach ($warehouses as $warehouse) {
            $warehouseType = $warehouse->type;
            $algorithmType = $warehouseAlgorithmTypesMap[$warehouseType];

            //$options[][$algorithmType] = [
            //    'code' => $warehouse->id,
            //    'label' => $warehouse->warehouse_id,
            //];

            $options[] = [
                'code' => $warehouse->id,
                'label' => $warehouse->warehouse_id,
            ];
        }

        return $options;
    }

    /**
     * @return array
     */
    protected function getProviderOptions()
    {
        $options = [];
        $products = \App\Models\Bizami\Precalculation::select(DB::raw('provider'))
            ->groupBy('provider')
            ->orderBy('provider')
            ->get();

        foreach ($products as $product) {
            $options[] = [
                'code' => $product->provider,
                'label' => $product->provider,
            ];
        }

        return $options;;
    }
}
