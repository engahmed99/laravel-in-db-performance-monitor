<?php

namespace ASamir\InDbPerformanceMonitor;

use Illuminate\Database\Eloquent\Model;

class LogIPs extends Model {

    protected $connection = 'inDbMonitorConn';
    protected $table = 'log_ips';
    protected $primaryKey = 'id';
    protected $fillable = [
        'ip', 'city', 'region', 'country', 'country_name', 'hostname', 'loc', 'org', 'total_c', 'total_c_error', 'is_finished'
    ];

    /**
     * Handle the IP Info. in the DB
     */
    public static function saveIPInfo() {
        $ip = LogIPs::where('ip', request()->ip())->first();
        $class = config('inDbPerformanceMonitor.IN_DB_MONITOR_GET_IP_CLASS');
        $info = [];
        if (!$ip) {
            if (request()->ip() == '127.0.0.1') {
                $info = $class::getIPInfo(request()->ip(), false);
                $info['is_finished'] = 1;
                $info['country_name'] = 'Localhost';
            } else
                $info = $class::getIPInfo(request()->ip(), config('inDbPerformanceMonitor.IN_DB_MONITOR_GET_IP_INFO'));
            $info['total_c'] = 1;
            LogIPs::create($info);
        } else if ($ip->is_finished == 0) {
            $info = $class::getIPInfo($ip->ip, config('inDbPerformanceMonitor.IN_DB_MONITOR_GET_IP_INFO'));
            $info['total_c'] = \DB::raw('total_c+1');
            LogIPs::where('ip', $ip->ip)->update($info);
        } else {
            $info['total_c'] = \DB::raw('total_c+1');
            LogIPs::where('ip', $ip->ip)->update($info);
        }
    }

    /**
     * The relation with LogRequests
     * @return type
     */
    public function request() {
        return $this->belongsTo('\ASamir\InDbPerformanceMonitor\LogRequests', 'ip', 'ip');
    }

}
