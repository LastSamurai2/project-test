<?php

namespace App\Console\Commands\Dataflows;

use App\Models\Dataflows\Runner;
use Illuminate\Console\Command;

class RunDataflows extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dataflow:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Dataflows.';

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
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $runner = new Runner();
        $runner->run();
    }
}
