<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Models\Dataflows;

/**
 * Class Runner
 * @package App\Models\Dataflows
 */
class Runner
{
    const MAX_EXECUTIONS = 5;
    const MAX_EXECUTIONS_TIME = 120;

    /**
     *
     */
    public function run()
    {
        $scheduler = new Scheduler();
        $scheduler->cleanExecutionList();
        $scheduler->generateExecutionList();

        $startTime = time();
        $executionsCounter = 0;
        $executedSchedules = [];
        $schedule = true;

        while ($schedule
            && ((time() - $startTime) < self::MAX_EXECUTIONS_TIME)
            && ($executionsCounter < self::MAX_EXECUTIONS)
        ) {
            $executionsCounter++;
            $schedule = $scheduler->getNextScheduleToExecute();
            if ($schedule && !array_key_exists($schedule->id, $executedSchedules)) {
                $this->executeSchedule($schedule);
                $executedSchedules[$schedule->id] = $schedule->id;
            }
        }
    }

    /**
     * @param $schedule
     */
    public function executeSchedule(Schedule $schedule)
    {
        $locker = new Locker();
        $profile = $schedule->getProfile();
        $report = new Report();

        if (!$profile->canBeExecuted()) {
            $schedule->setStatusLabel('Schedule Cannot Be Executed');
            return;
        }

        $report->initReport($schedule);
        $report->save();
        $profile->setReport($report);
        $canRun = true;

        try {
            $locker->lockSchedule($schedule);
        } catch (\Exception $e) {
            $report->setStatus(Report::STATUS_BLOCKED);
            $profile->addCriticalLog($e->getMessage());
            $profile->setResult('Blocked !');
            $canRun = false;
        }

        if ($canRun) {
            $report->beforeExecution();
            try {
                $profile->execute();
            } catch (\Exception $e) {
                $profile->addCriticalLog($e->getMessage());
                $profile->result = 'Critical Error !';
                $profile->setFailedStatus();
            }
            $report->afterExecution();
            $locker->unlockSchedule($schedule);
        }

        $report->save();
        $schedule->setResult($report->result);
        $schedule->setStatusLabel($report->getStatusLabel());
    }
}
