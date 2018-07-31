<?php

namespace ASamir\InDbPerformanceMonitor;

use Illuminate\Database\Eloquent\Model;

class LogQueries extends Model {

    protected $connection = 'inDbMonitorConn';
    protected $table = 'log_queries';
    protected $primaryKey = 'id';
    protected $fillable = [
        'query', 'bindings', 'time', 'connection_name', 'is_elequent', 'request_id'
    ];

    /**
     * Listen to DB queries and log them in the DB
     * @return type
     */
    public static function inDbLogQueries() {
        if (!request('__asamir_request_id'))
            return;
        $version = substr(app()->version(), 0, 3);
        if (in_array($version, ['5.0', '5.1']))
            \DB::listen(function ($sql, $bindings, $time, $connectionName) {
                $query = json_decode(json_encode(['sql' => $sql, 'bindings' => $bindings, 'time' => $time, 'connectionName' => $connectionName]));
                self::logQuery($query);
            });
        else
            \DB::listen(function ($query) {
                self::logQuery($query);
            });
    }

    /**
     * Log the query and update the request info
     * @param type $query
     * @return boolean
     */
    public static function logQuery($query) {
        if ($query->connectionName == 'inDbMonitorConn') //Prevent inifinite loop
            return true;
        // Save Queries Log
        LogQueries::create([
            'query' => (!is_string($query->sql)) ? $query->sql->getValue() : $query->sql,
            'bindings' => json_encode($query->bindings),
            'time' => $query->time,
            'connection_name' => ($query->connectionName) ?: 'default',
            'is_elequent' => (!is_string($query->sql)) ? 0 : 1,
            'request_id' => request('__asamir_request_id')
        ]);

        // Update request data
        LogRequests::find(request('__asamir_request_id'))->update([
            'queries_total_time' => \DB::raw('queries_total_time+' . $query->time),
            'queries_total_count' => \DB::raw('queries_total_count+1'),
            'queries_not_elequent_count' => \DB::raw('queries_not_elequent_count+' . ((!is_string($query->sql)) ? '1' : '0')),
        ]);
    }

    /**
     * The relation with LogRequests
     * @return type
     */
    public function request() {
        return $this->belongsTo('LogRequests', 'request_id');
    }

}
