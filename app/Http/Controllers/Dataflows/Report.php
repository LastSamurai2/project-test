<?php

namespace App\Http\Controllers\Dataflows;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class Report extends Controller
{

    public function index(Request $request)
    {

        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $query->where('result', 'LIKE', "%{$value}%");
            });
        });

        $reports = QueryBuilder::for(\App\Models\Dataflows\Report::class)
            ->defaultSort('executed_at')
            ->allowedSorts(['executed_at', 'finished_at'])
            ->allowedFilters(['type', 'user', 'status', $globalSearch])
            ->paginate(20)
            ->withQueryString();


        return Inertia::render('Dataflows/Report', [
            'reports' => $reports,
            'buttons' => [
                array(
                    "label" => "flow.schedule-manage-button",
                    "url" => route('dataflows.schedules')
                )
            ]
        ])->table(function (InertiaTable $table) {
            $table->addFilter('type', __('Type'), [
                'automatic' => __('Automatic'),
                'cli' => __('Cli'),
                'forced' => __('Forced'),
            ])->addFilter('status', 'Status', [
                'enabled' => 'Enabled',
                'disabled' => 'Disabled',
                'none' => __('None'),
                'not_finished' => __('Not Finished'),
                'blocked' => __('Blocked'),
                'success' => __('Succeded'),
                'nothing_to_do' => __('Nothing To Do'),
                'warning' => __('Warning'),
                'critical' => __('Critical'),
                'failed' => __('Failed')
            ])->addColumns([
                    'schedule_id' => 'Schedule',
                    'type' => "Type",
                    'executed_at' => "Executed At",
                    'finished_at' => "Finished At",
                    'result' => "Result",
                    'status' => __('Status')
                ]);
        });
    }
}
