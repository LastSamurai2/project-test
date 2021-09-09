<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Models\Dataflows;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class Scheduler
 * @package App\Models\Dataflows
 */
class Scheduler
{
    const EXECUTION_TIME_INTERVAL_IN_MINUTES = 5;
    const EXECUTION_TIME_AHEAD_IN_MINUTES = 30;
    const MISSED_EXECUTIONS_AFTER_IN_MINUTES = 15;
    const REMOVE_EXECUTIONS_AFTER_IN_HOURS = 48;

    /**
     *
     */
    public function cleanExecutionList()
    {
        $currentTime = Carbon::now()->timestamp;
        $missedTime = $currentTime - self::MISSED_EXECUTIONS_AFTER_IN_MINUTES * 60;
        $removeTime = $currentTime - self::REMOVE_EXECUTIONS_AFTER_IN_HOURS * 60 * 60;

        $missedExecutions = Execution::where('status', Execution::STATUS_PENDING)
            ->where('execute_at', '<=', strftime('%Y-%m-%d %H:%M', $missedTime))
            ->get();

        foreach ($missedExecutions as $execution) {
            $execution->status = Execution::STATUS_MISSED;
            $execution->save();
        }

        $executionsToRemove = Execution::where('execute_at', '<=', strftime('%Y-%m-%d %H:%M', $removeTime))
            ->get();

        foreach ($executionsToRemove as $execution) {
            $execution->delete();
        }
    }

    /**
     *
     */
    public function generateExecutionList()
    {
        $schedules = Schedule::where('status', Schedule::STATUS_ENABLED)
            ->orderBy('priority', 'desc')
            ->get();

        foreach ($schedules as $schedule) {
            $this->generateExecution($schedule);
        }
    }

    /**
     * @param $schedule
     */
    protected function generateExecution($schedule)
    {
        $timeInterval = self::EXECUTION_TIME_INTERVAL_IN_MINUTES * 60;
        $timeAhead = self::EXECUTION_TIME_AHEAD_IN_MINUTES * 60;
        $currentTime = Carbon::now()->timestamp;
        $currentTime -= $currentTime % $timeInterval;
        for ($timestamp = $currentTime; $timestamp < $currentTime + $timeAhead; $timestamp += $timeInterval) {
            try {
                $testScheduleResult = $this->testSchedule($timestamp, $schedule);
            } catch (\Exception $e) {
                return;
            }
            if ($testScheduleResult) {
                $execution = new Execution();
                $execution->schedule_id = $schedule->id;
                $execution->status = Execution::STATUS_PENDING;
                $execution->execute_at = strftime('%Y-%m-%d %H:%M', $timestamp);
                $this->trySaveExecution($execution);
            }
        }
    }

    /**
     * @param $execution
     */
    protected function trySaveExecution($execution)
    {
        try {
            $execution->save();
        } catch (\Exception $e) {
        }
    }

    /**
     * @param $timestamp
     * @param $schedule
     * @return bool
     */
    protected function testSchedule($timestamp, $schedule)
    {
        $cronExpression = explode(' ', $schedule->schedule);
        if (count($cronExpression) != 5 || !is_numeric($timestamp)) {
            return false;
        }

        $minutes = gmdate('i', $timestamp);
        $hours = gmdate('H', $timestamp);
        $monthDay = gmdate('j', $timestamp);
        $month = gmdate('n', $timestamp);
        $weekDay = gmdate('w', $timestamp);

        $match = $this->matchCronExpression($cronExpression[0], $minutes)
            && $this->matchCronExpression($cronExpression[1], $hours)
            && $this->matchCronExpression($cronExpression[2], $monthDay)
            && $this->matchCronExpression($cronExpression[3], $month)
            && $this->matchCronExpression($cronExpression[4], $weekDay);

        return $match;
    }

    protected function matchCronExpression($expr, $num)
    {
        // handle ALL match
        if ($expr === '*') {
            return true;
        }

        // handle multiple options
        if (strpos($expr, ',') !== false) {
            foreach (explode(',', $expr) as $e) {
                if ($this->matchCronExpression($e, $num)) {
                    return true;
                }
            }
            return false;
        }

        // handle modulus
        if (strpos($expr, '/') !== false) {
            $e = explode('/', $expr);
            if (sizeof($e) !== 2) {
                throw new \Exception('Invalid cron expression, expecting \'match/modulus\': ' . $expr);
            }
            if (!is_numeric($e[1])) {
                throw new \Exception('Invalid cron expression, expecting numeric modulus: ' . $expr);
            }
            $expr = $e[0];
            $mod = $e[1];
        } else {
            $mod = 1;
        }

        // handle all match by modulus
        if ($expr === '*') {
            $from = 0;
            $to = 60;
        } elseif (strpos($expr, '-') !== false) {
            // handle range
            $e = explode('-', $expr);
            if (sizeof($e) !== 2) {
                throw new \Exception('Invalid cron expression, expecting \'from-to\' structure:' . $expr);
            }

            $from = $this->getNumeric($e[0]);
            $to = $this->getNumeric($e[1]);
        } else {
            // handle regular token
            $from = $this->getNumeric($expr);
            $to = $from;
        }

        if ($from === false || $to === false) {
            throw new \Exception(__('Invalid cron expression: ' . $expr));
        }

        return $num >= $from && $num <= $to && $num % $mod === 0;
    }

    /**
     * @param $value
     * @return false|int|string
     */
    protected function getNumeric($value)
    {
        static $data = [
            'jan' => 1,
            'feb' => 2,
            'mar' => 3,
            'apr' => 4,
            'may' => 5,
            'jun' => 6,
            'jul' => 7,
            'aug' => 8,
            'sep' => 9,
            'oct' => 10,
            'nov' => 11,
            'dec' => 12,
            'sun' => 0,
            'mon' => 1,
            'tue' => 2,
            'wed' => 3,
            'thu' => 4,
            'fri' => 5,
            'sat' => 6,
        ];

        if (is_numeric($value)) {
            return $value;
        }

        if (is_string($value)) {
            $value = strtolower(substr($value, 0, 3));
            if (isset($data[$value])) {
                return $data[$value];
            }
        }

        return false;
    }

    /**
     *
     */
    public function getNextScheduleToExecute()
    {
        $forcedSchedule = Schedule::where('is_forced', 1)->first();
        if ($forcedSchedule) {
            $forcedSchedule->setExecutionType(Report::TYPE_FORCED);
            $forcedSchedule->is_forced = 0;
            $forcedSchedule->save();
            return $forcedSchedule;
        }

        $currentTime = strftime('%Y-%m-%d %H:%M', Carbon::now()->timestamp);
        $execution = Execution::where('status', Execution::STATUS_PENDING)
            ->where('execute_at', '<=', $currentTime)
            ->orderBy('execute_at', 'desc')
            ->first();

        if ($execution) {
            try {
                $schedule = Schedule::all()->find($execution->schedule_id);
                if ($schedule->status == Schedule::STATUS_ENABLED) {
                    $execution->status = Execution::STATUS_EXECUTED;
                    $execution->save();

                    /* if one execution is picked, mark other past same schedule exection as missed */
                    $missedExecutions = Execution::where('schedule_id', $execution->schedule_id)
                        ->where('status', Execution::STATUS_PENDING)
                        ->where('execute_at', '<=', $currentTime)
                        ->get();

                    foreach ($missedExecutions as $missedExecution) {
                        $missedExecution->status = Execution::STATUS_MISSED;
                        $missedExecution->save();
                    }

                    return $schedule;
                }
            } catch (\Exception $e) {
                $execution->status = Execution::STATUS_ERROR;
                $execution->save();
            }
        }

        return false;
    }
}
