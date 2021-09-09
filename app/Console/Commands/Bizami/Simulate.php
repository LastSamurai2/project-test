<?php

namespace App\Console\Commands\Bizami;

use App\Models\Bizami\ZmProcessor;
use App\Models\Dataflows\Schedule\ArtisanCommandOutput;
use Illuminate\Console\Command;

class Simulate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bizami:simulate {provider} {algorithm_id} {product_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Bizami Simulation';

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
        $provider = $this->argument('provider');
        $algorithm = $this->argument('algorithm_id');

        $zmProcessr = new ZmProcessor();
        $zmProcessr->setProvider($provider);
        $zmProcessr->setAlgorithm($algorithm);

        $productId = $this->argument('product_id');
        if ($productId) {
            $zmProcessr->setProductIds($productId);
        }

        $logOutput = new ArtisanCommandOutput($this);
        $zmProcessr->addOutputModel($logOutput);
        $result = $zmProcessr->execute();

        foreach ($result as $productId => $calculatedData) {
            $this->info($productId . ': ');
            $this->info(' - zm: ' . $calculatedData['zm']);
            $this->info(' - zm_before_correction: ' . $calculatedData['zm_before_correction']);
            $this->info(' - seasonability: ' . $calculatedData['seasonability']);
            $this->info(' - mediana: ' . $calculatedData['mediana']);
            $this->info(' - rotation_qty: ' . $calculatedData['rotation_qty']);
            $this->info(' - rotation_value: ' . $calculatedData['rotation_value']);
            $this->info(' - in stock qty: ' . $calculatedData['in_stock_qty']);
            $this->info(' - ordered qty: ' . $calculatedData['ordered_qty']);
            $this->info(' - reserved qty: ' . $calculatedData['reserved_qty']);
            $this->info(' - t: ' . $calculatedData['t']);
            $this->info(' - picks corrected: ' . (int)$calculatedData['correct_picks_flag']);
        }

        $this->info('Total: ' . count($result));

        return 0;
    }
}
