<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Models\Dataflows\Schedule;

use App\Models\Dataflows\Report\Log;

class ArtisanCommandOutput
{
    /**
     * @var
     */
    protected $command;

    /**
     * @param $command
     */
    public function __construct($command)
    {
        $this->command = $command;
    }

    /*
     *
     */
    public function output($log)
    {
        if (is_object($log)) {
            $message = $log->message;
            $level = $log->level;
        } else {
            $level = Log::LOG_LEVEL_INFO;
            $message = $log;
        }

        switch ($level) {
            case Log::LOG_LEVEL_CRITICAL:
                $this->command->error($message);
                break;
            case Log::LOG_LEVEL_WARNING:
                $this->command->warn($message);
                break;
            default:
                $this->command->info($message);
        }
    }
}
