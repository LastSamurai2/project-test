<?php

namespace App\Http\Controllers\Dataflows;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class Schedule extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {

        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $query->where('name', 'LIKE', "%{$value}%")->orWhere('code', 'LIKE', "%{$value}%");
            });
        });

        $schedules = QueryBuilder::for(\App\Models\Dataflows\Schedule::class)
            ->defaultSort('id')
            ->allowedSorts(['name', 'code', 'priority'])
            ->allowedFilters(['name', 'code', 'status', 'is_forced', $globalSearch])
            ->paginate(10)
            ->withQueryString();


        return Inertia::render('Dataflows/Schedule', [
            'schedules' => $schedules,
            'columns' => [
                'is_forced' => [
                    'label' => __('is_forced'),
                    'type' => 'select',
                    'options' => [
                        ['label' => __('yes'), 'code' => 1],
                        ['label' => __('no'), 'code' => 0]
                    ],
                ],
                'status' => [
                    'label' => __('status'),
                    'type' => 'select',
                    'options' => [
                        ['label' => __('enabled'), 'code' => 'enabled'],
                        ['label' => __('disabled'), 'code' => 'disabled']
                    ],
                ],
            ],
            'buttons' => [
                array(
                    "label" => "button.back",
                    "url" => route('dataflows.report')
                )
            ]
        ])->table(function (InertiaTable $table) {
            $table->addSearchRows([
                'name' => __('name'),
                'code' => __('code'),
            ])->addColumns([
                'name' => __('name'),
                'code' => __('code'),
                'schedule' => __('dataflow.schedule.title')
            ])->addFilter('status', __('status'), [
                'enabled' => __('enabled'),
                'disabled' => __('disabled'),
            ])->addFilter('is_forced', __('dataflow.is_forced'), [
                '0' => __('no'),
                '1' => __('yes'),
            ]);
        });
    }

    /**
     * @param $id
     * @return \Inertia\Response
     */
    public function edit($id)
    {

        $schedules = \App\Models\Dataflows\Schedule::all();
        $schedule = $schedules->find($id);
        $schedule->initParameters();

        return Inertia::render('Dataflows/Schedule/Edit', [
            'id' => $id,
            'schedule' => $schedule,
            'fields' => [
                'is_forced' => [
                    'label' => 'Is Forced',
                    'type' => 'select',
                    'required' => true,
                    'readonly' => false,
                    'options' => [
                        ['label' => 'Yes', 'code' => 1],
                        ['label' => 'No', 'code' => 0]
                    ],
                ],
                'status' => [
                    'label' => 'Status',
                    'type' => 'select',
                    'required' => true,
                    'readonly' => false,
                    'options' => [
                        ['label' => 'Enabled', 'code' => 'enabled'],
                        ['label' => 'Disabled', 'code' => 'disabled']
                    ],
                ],
            ],
            'buttons' => [
                array(
                    "label" => "button.back",
                    "url" => route('dataflows.schedules')
                )
            ]
        ]);
    }

    /**
     * @param Request $request
     * @param $scheduleId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(\Illuminate\Http\Request $request, $scheduleId)
    {
        \Illuminate\Support\Facades\Request::validate([
            'name' => ['required'],
            'schedule' => ['required']
        ]);

        $schedules = \App\Models\Dataflows\Schedule::all();
        $schedule = $schedules->find($scheduleId);

        $columns = Schema::getColumnListing($schedule->getTable());
        foreach($columns as $key)
        {
            if (isset($request->$key)) {
                $schedule->$key = $request->$key;
            }
        }

        $schedule->save();

        return Redirect::back()->withFlash(['success' =>  __('dataflow.schedule.flash.updated')]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param $scheduleId
     */
    public function addToQueue(\Illuminate\Http\Request $request, $scheduleId)
    {
        $schedules = \App\Models\Dataflows\Schedule::all();
        $schedule = $schedules->find($scheduleId);
        $schedule->is_forced = 1;
        $schedule->save();

        return Redirect::back()->withFlash(['success', __('dataflow.schedule.flash.added_queue')]);
    }

}
