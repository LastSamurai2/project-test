<?php

namespace App\Http\Controllers\Bizami;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class Algorithm
 * @package App\Http\Controllers\Bizami
 */
class Algorithm extends Controller
{

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $query->where('name', 'LIKE', "%{$value}%")->orWhere('description', 'LIKE', "%{$value}%");
            });
        });

        $algorithm = new \App\Models\Bizami\Algorithm();
        $allowedSorts = Schema::getColumnListing($algorithm->getTable());

        $algorithms = QueryBuilder::for(\App\Models\Bizami\Algorithm::class)
            ->defaultSort('id')
            ->allowedSorts($allowedSorts)
            ->allowedFilters(['type', 'name', 'description', $globalSearch])
            ->paginate(10)
            ->withQueryString();


        return Inertia::render('Bizami/Algorithm/List', [
            'algorithms' => $algorithms,
            'columns' => [
                'type' => [
                    'options' => $this->getTypeOptions()->getOptions(),
                ],
            ],
            'buttons' => [
                array(
                    "label" => "button.add-new",
                    "url" => route('bizami.algorithm.new')
                )
            ]
        ])->table(function (InertiaTable $table) {
            $table->addFilter(
                'type', __(__('bizami.algorithm.field.type')), $this->getTypeOptions()->getOptionsArray()
            )->addSearchRows([
                'name' => __('bizami.name'),
                'description' => __('bizami.description')
            ])->addColumns([
            ]);
        });
    }

    /**
    * @param $id
    * @return \Inertia\Response
    */

    public function edit($id = null)
    {
        if ($id) {
            $algorithms = \App\Models\Bizami\Algorithm::all();
            $algorithm = $algorithms->find($id);
        } else {
            $algorithm = new \App\Models\Bizami\Algorithm();
        }

        return Inertia::render('Bizami/Algorithm/Edit', [
            'algorithm' => $algorithm,
            'fields' => [
                'type' => [
                    'options' => $this->getTypeOptions()->getOptions(),
                ],
            ],
            'buttons' => [
                array(
                    "label" => "button.back",
                    "url" => route('bizami.algorithm')
                )
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validateAlgorithmRequest($request);

        $algorithms = \App\Models\Bizami\Algorithm::all();
        $algorithm = $algorithms->find($id);
        $this->setAlgorithmData($algorithm, $request);
        $algorithm->save();

        return Redirect::route('bizami.algorithm')->withFlash(['success' => __('bizami.algorithm.flash.updated')]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateAlgorithmRequest($request);
        $algorithm = new \App\Models\Bizami\Algorithm();
        $this->setAlgorithmData($algorithm, $request);
        $algorithm->save();


        return Redirect::route('bizami.algorithm')->withFlash(['success' => __('bizami.algorithm.flash.saved')]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(\Illuminate\Http\Request $request, $id)
    {
        \App\Models\Bizami\Algorithm::destroy($id);

        return Redirect::route('bizami.algorithm')->withFlash(['success' => __('bizami.algorithm.flash.deleted')]);
    }

    /**
     * @param $request
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateAlgorithmRequest($request)
    {
        // TODO: this must stay static - I think.
        $this->validate(
            $request,
            [
                'name' => 'required',
                'type' => 'required'
            ]);
    }

    /**
     * @param $algorithm
     * @param $request
     */
    protected function setAlgorithmData($algorithm, $request)
    {
        $columns = Schema::getColumnListing($algorithm->getTable());
        foreach($columns as $key)
        {
            if (isset($request->$key)) {
                $algorithm->$key = $request->$key;
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    protected function getTypeOptions()
    {
        $algorithm = new \App\Models\Bizami\Algorithm();
        $options = $algorithm->getTypeOptions();
        return $options;
    }
}
