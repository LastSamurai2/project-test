<?php

namespace App\Console\Commands\Dataflows;

use App\Models\Dataflows\Schedule\ArtisanCommandOutput;
use App\Models\Dataflows\Report;
use App\Models\Dataflows\Schedule;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RunDataflowSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dataflow:schedule:run {code}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Dataflow Schedule by code.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
     */
    public function handle()
    {
        $scheduleCode = $this->argument('code');
        $schedule = null;

        try {
            $schedule = Schedule::where('code', $scheduleCode)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            $this->error('Schedule Not Found.');
            return;
        }

        $logOutput = new ArtisanCommandOutput($this);
        $schedule->setExecutionType(Report::TYPE_CLI);
        $schedule->addOutputModel($logOutput);

        try {
            $schedule->execute();
            $this->info('Schedule has been finished with result: ' . $schedule->getResult());
        } catch (\Exception $e) {
            $this->error('Schedule has been failed with exception: ' . $e->getMessage());
        }

        $this->info('Schedule Result Status: ' . $schedule->getStatusLabel());
    }
}
