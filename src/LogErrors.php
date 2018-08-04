<?php

namespace ASamir\InDbPerformanceMonitor;

use Illuminate\Database\Eloquent\Model;

class LogErrors extends Model {

    protected $connection = 'inDbMonitorConn';
    protected $table = 'log_errors';
    protected $primaryKey = 'id';
    protected $fillable = [
        'message', 'code', 'file', 'line', 'trace', 'request_id'
    ];

    /**
     * Log error in the DB
     * @param \Exception $exception
     * @return type
     */
    public static function inDbLogError(\Exception $exception) {
        if (!request('__asamir_request_id'))
            return;
        // Save Errors Log
        LogErrors::create([
            'message' => str_replace(base_path(), "~BASE_PATH~", $exception->getMessage()),
            'code' => $exception->getCode(),
            'file' => str_replace(base_path(), "~BASE_PATH~", $exception->getFile()),
            'line' => $exception->getLine(),
            'trace' => "<b>BASE_PATH = \"" . base_path() . "\"</b>\n" . str_replace(base_path(), "~BASE_PATH~", $exception->getTraceAsString()),
            'request_id' => request('__asamir_request_id'),
        ]);

        // Update request data
        LogRequests::find(request('__asamir_request_id'))->update([
            'has_errors' => 1,
        ]);
    }

    /**
     * The relation with LogRequests
     * @return type
     */
    public function request() {
        return $this->hasOne('LogRequests', 'request_id');
    }

}
