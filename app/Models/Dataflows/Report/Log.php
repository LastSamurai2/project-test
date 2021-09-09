<?php

namespace App\Models\Dataflows\Report;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Log extends Model
{
    const LOG_LEVEL_INFO = 'info';
    const LOG_LEVEL_CRITICAL = 'critical';
    const LOG_LEVEL_WARNING = 'warning';

    const MAX_LOG_MESSAGE_LENGTH = '1000';

    protected $table = 'alekseon_dataflow_report_log';

    /**
     * @return false
     */
    public function getUpdatedAtColumn()
    {
        return null;
    }

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s'
    ];

    /**
     * @return false|mixed
     */
    public function getLastLogDate($level = self::LOG_LEVEL_CRITICAL)
    {
        $logs = DB::table($this->getTable())->select('created_at')
            ->where('level', $level)
            ->orderBy('created_at', 'desc')
            ->limit(1);

        $logsCollection = $logs->get();

        foreach ($logsCollection as $log) {
            return $log->created_at;
        }

        return false;
    }
}
