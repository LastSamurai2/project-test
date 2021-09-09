<?php

namespace App\Models\Dataflows;

use App\Models\Dataflows\Report\Log;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    const TYPE_AUTOMATIC = 'automatic';
    //const TYPE_MANUAL = 'manual';
    const TYPE_FORCED = 'forced';
    const TYPE_CLI = 'cli';

    const STATUS_NONE = 'none';
    const STATUS_NOT_FINISHED = 'not_finished';
    const STATUS_BLOCKED = 'blocked';
    const STATUS_SUCCEDED = 'success';
    const STATUS_NOTHING_TO_DO = 'nothing_to_do';
    const STATUS_WARNING = 'warning';
    const STATUS_CRITICAL = 'critical';
    const STATUS_FAILED = 'failed';

    protected $table = 'alekseon_dataflow_report';

    protected $outputModels = [];
    protected $logCounters = [];
    protected $schedule;

    /**
     * @return false
     */
    public function usesTimestamps()
    {
        return false;
    }

    /**
     * @return array
     */
    public function getAvailableTypes()
    {
        return [
            self::TYPE_AUTOMATIC => __('Automatic'),
            self::TYPE_CLI => __('Cli'),
            self::TYPE_FORCED => __('Forced'),
        ];
    }

    /**
     * @return array[]
     */
    public function getAllStatuses()
    {
        /**
         * sorted by priorities
         */
        return [
            self::STATUS_NONE => [
                'label' => __('None'),
                'priority' => 0,
            ],
            self::STATUS_NOT_FINISHED => [
                'label' => __('Not Finished'),
                'priority' => 1,
            ],
            self::STATUS_BLOCKED => [
                'label' => __('Blocked'),
                'priority' => 2,
            ],
            self::STATUS_SUCCEDED => [
                'label' => __('Succeded'),
                'priority' => 3,
            ],
            self::STATUS_NOTHING_TO_DO => [
                'label' => __('Nothing To Do'),
                'priority' => 4,
            ],
            self::STATUS_WARNING => [
                'label' => __('Warning'),
                'priority' => 5,
            ],
            self::STATUS_CRITICAL => [
                'label' => __('Critical'),
                'priority' => 6,
            ],
            self::STATUS_FAILED => [
                'label' => __('Failed'),
                'priority' => 7,
            ],
        ];
    }

    /**
     * @param $status
     * @return int|mixed
     */
    protected function getStatusPriority($status)
    {
        $priority = 0;

        $statuses = $this->getAllStatuses();
        if (isset($statuses[$status]['priority'])) {
            return $statuses[$status]['priority'];
        }

        return $priority;
    }

    /**
     * @param $status
     */
    public function setStatus($status)
    {
        $priority = $this->getStatusPriority($status);
        $currentStatusPriority = $this->getStatusPriority($this->status);

        if (!$currentStatusPriority) {
            $currentStatusPriority = 0;
        }

        if ($priority !== false && $priority >= $currentStatusPriority) {
            $this->status =  $status;
        }

        return $this;
    }

    /**
     * @return Schedule[]|\Illuminate\Database\Eloquent\Collection|Model|null
     */
    public function getSchedule()
    {
        if ($this->schedule == null) {
            $schedules = Schedule::all();
            $this->schedule = $schedules->find($this->schedule_id);
        }
        return $this->schedule;
    }

    /**
     * @param $schedule
     */
    public function initReport($schedule)
    {
        $this->schedule_id = $schedule->id;
        $this->outputModels = $schedule->getOutputModels();

        if ($schedule->getExecutionType()) {
            $this->type = $schedule->getExecutionType();
        } else {
            $this->type = self::TYPE_AUTOMATIC;
        }

        $this->user = 'system';
        $this->status = self::STATUS_NOT_FINISHED;
    }

    /**
     *
     */
    public function beforeExecution()
    {
        $this->addLog(Log::LOG_LEVEL_INFO, 'Start Profile Execution.');
    }

    /**
     *
     */
    public function afterExecution()
    {
        $this->setStatus(self::STATUS_SUCCEDED);
        $this->addLog(Log::LOG_LEVEL_INFO, 'End Profile Execution.');
        $this->finished_at = new Carbon();
    }

    /**
     *
     */
    public function addLog($level, $message)
    {
        if ($this->id) {
            $log = new Log();
            $log->report_id = $this->id;
            $log->level =  $level;

            if (strlen($message) > Log::MAX_LOG_MESSAGE_LENGTH) {
                $message = substr($message, 0, Log::MAX_LOG_MESSAGE_LENGTH);
            }

            $log->message = $message;
            $log->save();
            foreach ($this->outputModels as $outputModel) {
                $outputModel->output($log);
            }

            if (!isset($this->logCounters[$level])) {
                $this->logCounters[$level] = 0;
            }
            $this->logCounters[$level] ++;
        }
    }

    /**
     * @param $level
     */
    public function getLogCounter($level)
    {
        if (isset($this->logCounters[$level])) {
            return (int) $this->logCounters[$level];
        }
        return 0;
    }

    /**
     * @return mixed
     */
    public function getStatusLabel()
    {
        $allStatuses = $this->getAllStatuses();
        $status = $this->status;
        if (!isset($allStatuses[$status])) {
            $status = self::STATUS_NONE;
        }
        return $allStatuses[$status]['label'];
    }
}
