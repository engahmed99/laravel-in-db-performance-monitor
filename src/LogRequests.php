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

    /**
     * Start the request log operation
     * Log Request
     * Log Queries
     * Log Error
     */
    public static function startInDbMonitor() {

        // Save request data
        $req = LogRequests::create([
                    'action' => request()->getPathInfo(),
                    'parameters' => serialize(self::getRequestFields()),
                    'type' => strtoupper(request()->method() . ((request()->ajax()) ? '-AJAX' : '')),
                    'url' => request()->url(),
                    'ip' => request()->ip(),
        ]);
        // Store IP info.
        LogIPs::saveIPInfo();

        request()->request->add(['__asamir_request_id' => $req->id]);

        // Log queries
        LogQueries::inDbLogQueries();
    }

    /**
     * Return request data after adjusting hidden parameters
     * @return array
     */
    public static function getRequestFields() {
        $data = request()->all();
        if (!$data)
            return $data;
        if (config('inDbPerformanceMonitor.IN_DB_MONITOR_NEGLICT_REQUEST_DATA') == true)
            return ['%__ALL_HIDDEN__%'];

        $neglict = config('inDbPerformanceMonitor.IN_DB_MONITOR_NEGLICT_PARAMS_CONTAIN');
        foreach ($data as $k => $v)
            foreach ($neglict as $n)
                if (strpos(trim(strtolower($k)), trim(strtolower($n))) !== false)
                    $data[$k] = '%_HIDDEN_%';
        return $data;
    }

    /**
     * Check if the path is valid to be monitored 
     * by checking the config variables IN_DB_MONITOR_WORK and IN_DB_MONITOR_NEGLICT_START_WITH
     * @param type $pathInfo
     * @return boolean
     */
    public static function isValidPathToLog($pathInfo) {
        if (!config('inDbPerformanceMonitor.IN_DB_MONITOR_WORK'))
            return false;
        foreach (config('inDbPerformanceMonitor.IN_DB_MONITOR_NEGLICT_START_WITH') as $path)
            if (substr($pathInfo, 0, strlen($path)) == $path)
                return false;
        return true;
    }

    /**
     * The relation with LogQueries
     * @return type
     */
    public function queries() {
        return $this->hasMany('\ASamir\InDbPerformanceMonitor\LogQueries', 'request_id');
    }

    /**
     * The relation with LogErrors
     * @return type
     */
    public function error() {
        return $this->hasOne('\ASamir\InDbPerformanceMonitor\LogErrors', 'request_id');
    }

    /**
     * The relation with LogIPs
     * @return type
     */
    public function ip_info() {
        return $this->hasOne('\ASamir\InDbPerformanceMonitor\LogIPs', 'ip', 'ip');
    }

}
