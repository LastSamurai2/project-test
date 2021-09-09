<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Models\Dataflows;

use App\Models\Dataflows\DataSource\DataSourceInterface;
use App\Models\Dataflows\Profile\DataReader;

/**
 * Class Profile
 * @package App\Dataflows\Profile
 */
abstract class Profile implements \App\Models\Dataflows\ProfileInterface
{
    /**
     * @var
     */
    protected $schedule;
    /**
     * @var
     */
    protected $report;

    /**
     * @param Schedule $schedule
     * @return $this
     */
    public function setSchedule(Schedule $schedule)
    {
        $this->schedule = $schedule;
        return $this;
    }

    /**
     * @param Report $report
     * @return $this
     */
    public function setReport(Report $report)
    {
        $this->report = $report;
        return $this;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getReport()
    {
        if (!$this->report) {
            throw new \Exception('Report is not set for profile.');
        }
        return $this->report;
    }

    /**
     * @param DataSourceInterface $dataSource
     * @return DataReader
     * @throws \Exception
     */
    public function getDataReader(DataSourceInterface $dataSource)
    {
        $dataReader = new DataReader();
        $dataReader->setDataSource($dataSource);
        return $dataReader;
    }

    /**
     * @return bool
     */
    public function canManualRun()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function canBeEnabled()
    {
        return true;
    }

    /**
     * @param $result
     * @return $this
     */
    public function setResult($result)
    {
        $this->report->result = $result;
        return $this;
    }

    /**
     * @param $message
     * @return $this
     * @throws \Exception
     */
    public function addInfoLog($message)
    {
        $this->getReport()->addLog(Report\Log::LOG_LEVEL_INFO, $message);
        return $this;
    }

    /**
     * @param $message
     * @return $this
     * @throws \Exception
     */
    public function addWarningLog($message)
    {
        $this->setWarningStatus();
        $this->getReport()->addLog(Report\Log::LOG_LEVEL_WARNING, $message);
        return $this;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getWarningsCounterValue()
    {
        return $this->getReport()->getLogCounter(Report\Log::LOG_LEVEL_WARNING);
    }

    /**
     * @param $message
     * @return $this
     * @throws \Exception
     */
    public function addCriticalLog($message)
    {
        $this->setCriticalStatus();
        $this->getReport()->addLog(Report\Log::LOG_LEVEL_CRITICAL, $message);
        return $this;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getCriticalCounterValue()
    {
        return $this->getReport()->getLogCounter(Report\Log::LOG_LEVEL_CRITICAL);
    }

    /**
     * @return false
     */
    public function getMaxLockTime()
    {
        return false;
    }

    /**
     * @param $paramCode
     * @return false|mixed
     */
    public function getParam($paramCode)
    {
        $parameters = $this->schedule->getParameters();
        if (isset($parameters[$paramCode])) {
            return $parameters[$paramCode];
        }
        return false;
    }

    /**
     * @param $paramCode
     * @param $value
     * @return $this
     */
    public function setParam($paramCode, $value)
    {
        $parameters = $this->schedule->getParameters();
        $parameters[$paramCode] = $value;
        $this->schedule->setParameters($parameters);
        return $this;
    }

    /**
     * @return array
     */
    public function getParametersFormConfig()
    {
        return [];
    }

    /**
     * @return bool
     */
    public function canBeExecuted()
    {
        return true;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getStatusLabel()
    {
        return $this->getReport()->getStatusLabel();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function setFailedStatus()
    {
        $this->getReport()->status = Report::STATUS_FAILED;
        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function setWarningStatus()
    {
        $this->getReport()->status  = Report::STATUS_WARNING;
        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function setCriticalStatus()
    {
        $this->getReport()->status = Report::STATUS_CRITICAL;
        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function setNothingToDoStatus()
    {
        $this->getReport()->status = Report::STATUS_NOTHING_TO_DO;
        return $this;
    }
}
