<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Models\Dataflows;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Execution
 * @package App\Models\Dataflows
 */
class Execution extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_EXECUTED = 'executed';
    const STATUS_MISSED = 'missed';
    const STATUS_ERROR = 'error';

    protected $table = 'alekseon_dataflow_execution';

    /**
     * @return false
     */
    public function usesTimestamps()
    {
        return false;
    }

    /**
     * @param array $options
     * @return bool|void
     */
    public function save(array $options = [])
    {
        if (!$this->id) {
            $currentExecutions = Execution::where('schedule_id', $this->schedule_id)
                ->where('execute_at', $this->execute_at);

            if ($currentExecutions->count()) {
                throw new \Exception('Execution already exists.');
            }
        }
    }
}
