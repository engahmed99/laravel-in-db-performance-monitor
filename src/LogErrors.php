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
        // Return if request not found
        // Or the error is from this class [To prevent looping]
        if (!request('__asamir_request_id') || $exception->getFile() == __DIR__ . '\LogErrors.php')
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

        // Update ip
        LogIPs::where('ip', request()->ip())->update(['total_c_error' => \DB::raw('total_c_error+1')]);

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
        return $this->hasOne('\ASamir\InDbPerformanceMonitor\LogRequests', 'id', 'request_id');
    }

}
