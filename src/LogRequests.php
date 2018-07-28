<?php

namespace ASamir\InDbPerformanceMonitor;

use Illuminate\Database\Eloquent\Model;

class LogRequests extends Model {

    protected $connection = 'inDbMonitorConn';
    protected $table = 'log_requests';
    protected $primaryKey = 'id';
    protected $fillable = [
        'action', 'parameters', 'type', 'url', 'session_id', 'session_data', 'ip', 'queries_total_time',
        'queries_total_count', 'queries_not_elequent_count', 'exec_time', 'route_uri', 
        'route_static_prefix', 'has_errors', 'is_json_response', 'archive_tag'
    ];

    public static function startInDbMonitor() {

        $req = LogRequests::create([
                    'action' => request()->getPathInfo(),
                    'parameters' => json_encode(request()->all()),
                    'type' => request()->method(),
                    'url' => request()->url(),
                    'session_serrialized' => serialize(session()->all()),
                    'ip' => request()->ip(),
        ]);
        request()->request->add(['__asamir_request_id' => $req->id]);
        LogQueries::inDbLogQueries();
    }

    public static function isValidPathToLog($pathInfo) {
        if (!config('inDbPerformanceMonitor.IN_DB_MONITOR_WORK'))
            return false;
        foreach (config('inDbPerformanceMonitor.IN_DB_MONITOR_NEGLICT_START_WITH') as $path)
            if (substr($pathInfo, 0, strlen($path)) == $path)
                return false;
        return true;
    }

    public function queries() {
        return $this->hasMany('\ASamir\InDbPerformanceMonitor\LogQueries', 'request_id');
    }

    public function error() {
        return $this->hasOne('\ASamir\InDbPerformanceMonitor\LogErrors', 'request_id');
    }

}
