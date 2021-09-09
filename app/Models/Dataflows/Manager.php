<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Models\Dataflows;

/**
 * Class Manager
 * @package App\Models\Dataflows
 */
class Manager
{
    /**
     * @param $code
     * @param array $data
     * @param false $graceful
     */
    public static function createSchedule($code, $data = [], $graceful = false)
    {
        $schedule = new Schedule();
        foreach ($data as $key => $value) {
            $schedule->$key = $value;
        }
        $schedule->code = $code;

        try {
            $schedule->save();
        } catch (\Exception $e) {
            if (!$graceful) {
                throw new \Exception('Unable to save schedule ' . $code . ': ' . $e->getMessage());
            }
        }

        return $schedule;
    }

    /**
     * @param $code
     */
    public static function deleteSchedule($code, $graceful = false)
    {
        $schedules = Schedule::all()->where('code', $code);
        foreach ($schedules as $schedule) {
            $schedule->delete();
        }
    }

    /**
     * @param $code
     * @param array $data
     * @param false $graceful
     */
    public static function updateSchedule($code, $data = [], $graceful = false)
    {
        $schedules = Schedule::all()->where('code', $code);

        if (!$schedules->count() && !$graceful) {
            throw new \Exception('Unable to update schedule' . $code . ': this schedule doesnt exists');
        }

        foreach ($schedules as $schedule) {
            foreach ($data as $key => $value) {
                $schedule->$key = $value;
            }

            try {
                $schedule->save();
            } catch (\Exception $e) {
                if (!$graceful) {
                    throw new \Exception('Unable to update schedule ' . $code . ': ' . $e->getMessage());
                }
            }
        }
    }
}
