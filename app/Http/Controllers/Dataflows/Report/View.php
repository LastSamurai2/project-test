<?php

namespace App\Http\Controllers\Dataflows\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use function GuzzleHttp\Promise\all;

class View extends Controller
{

    public function index(Request $request, $id)
    {
        $reports = \App\Models\Dataflows\Report::all();
        $report = $reports->find($id);
        $report->status_label = $report->getStatusLabel();
        $report->schedule_name = $report->getSchedule()->name;

        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $query->where('message', 'LIKE', "%{$value}%");
            });
        });

        $logs = QueryBuilder::for(\App\Models\Dataflows\Report\Log::class)
            ->where('report_id', $id)
            ->defaultSort('created_at')
            ->allowedSorts(['created_at'])
            ->allowedFilters(['id', 'created_at', 'message', 'level', $globalSearch])
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Dataflows/Report/View', [
            'report' => $report,
            'logs' => $logs,
            'buttons' => [
                array(
                    "label" => "button.back",
                    "url" => route('dataflows.report')
                )
            ]
        ])->table(function (InertiaTable $table) {
            $table->addSearchRows([
                'id' => 'ID',
                'created_at' => 'Log Time',
                'level' => 'Level'
            ])->addColumns(
                [
                    'message' => 'Message',
                    'created_at' => 'Created At',
                    'level' => 'Level',
                ],
            );
        });
    }
}
