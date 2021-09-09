<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Models\Dataflows;

use App\Models\Dataflows\Report;
use App\Models\Dataflows\Schedule;

/**
 * Class ProfileInterface
 * @package App\Dataflows\Profile
 */
interface ProfileInterface
{
    public function execute();

    public function getParametersFormConfig();

    public function getParam($paramCode);

    public function getMaxLockTime();

    public function addInfoLog($message);

    public function addWarningLog($message);

    public function addCriticalLog($message);

    public function setResult($result);

    public function canManualRun();

    public function setReport(Report $report);

    public function setSchedule(Schedule $schedule);
}
