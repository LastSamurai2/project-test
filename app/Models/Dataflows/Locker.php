<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Models\Dataflows;

use Illuminate\Support\Facades\Storage;

/**
 * Class Locker
 * @package App\Models\Dataflows
 */
class Locker
{
    const LOCKS_DIRECTORY_NAME = 'dataflow_locks';
    const LOCK_EXTENSION = '.lock';

    /**
     * @var array
     */
    protected $scheduleLockFilePaths = [];

    /**
     * @param $schedule
     */
    public function lockSchedule($schedule)
    {
        $counter = 0;
        while ($this->isScheduleLocked($schedule)) {
            sleep(2); // wait 2 seconds
            $counter ++;
            if ($counter == 5) {
                throw new \Exception('Schedule is blocked.');
            }
        }

        $maxLockTime = $schedule->getProfile()->getMaxLockTime();

        if ($maxLockTime) {
            $maxTime = time() + $maxLockTime;
        } else {
            $maxTime = 'still';
        }

        $lockFilePaths = $this->getLockFileNames($schedule);
        foreach ($lockFilePaths as $lockFilePath) {
            $this->getLockfilesStorage()->put($lockFilePath, $maxTime);
        }
    }

    /**
     * @param $schedule
     */
    public function unlockSchedule($schedule)
    {
        $lockFilePaths = $this->getLockFileNames($schedule);
        foreach ($lockFilePaths as $lockFilePath) {
            $this->getLockfilesStorage()->delete($lockFilePath);
        }
    }

    /**
     * @param $schedule
     * @return mixed|string[]
     */
    protected function getLockFileNames($schedule)
    {
        if (!isset($this->scheduleLockFilePaths[$schedule->id])) {
            $filePaths = [
                'schedule' => $this->getFileName('schedule_' . $schedule->code),
            ];
            $sempahores = $schedule->getSemaphores();
            if ($sempahores) {
                foreach ($sempahores as $semaphore) {
                    $filePaths[$semaphore] = $this->getFileName('semaphore_' . $semaphore);
                }
            }
            $this->scheduleLockFilePaths[$schedule->id] = $filePaths;
        }

        return $this->scheduleLockFilePaths[$schedule->id];
    }

    /**
     * @param $schedule
     */
    protected function isScheduleLocked($schedule)
    {
        $lockFilePaths = $this->getLockFileNames($schedule);
        foreach ($lockFilePaths as $lockFilePath) {
            if ($this->getLockfilesStorage()->exists($lockFilePath)) {
                try {
                    $lockTime = $this->getLockfilesStorage()->get($lockFilePath);
                    if ($lockTime == 'still') {
                        return true;
                    }
                    if (time() < (int) $lockTime) {
                        return true;
                    }
                    $this->getLockfilesStorage()->delete($lockFilePath);
                } catch (\Exception $e) {
                }
            }
        }
        return false;
    }

    /**
     * @param $name
     * @return string
     */
    protected function getFileName($name)
    {
        return  self::LOCKS_DIRECTORY_NAME . DIRECTORY_SEPARATOR . $name . self::LOCK_EXTENSION;
    }

    /**
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected function getLockfilesStorage()
    {
        return Storage::disk('local');
    }
}
