<?php

return [
    /**
     * If true allow log requests
     */
    'IN_DB_MONITOR_WORK' => env('IN_DB_MONITOR_WORK', true),
    /**
     * If true allow /admin-monitor routes eles abort(404)
     */
    'IN_DB_MONITOR_PANEL' => env('IN_DB_MONITOR_PANEL', true),
    /**
     * Password token
     */
    'IN_DB_MONITOR_TOKEN' => '%SET_TOKEN%',
    /**
     * Put all routes you don't want to monitor
     * e.x. all routes which start with /admin-monitor will be neglected from log
     */
    'IN_DB_MONITOR_NEGLICT_START_WITH' => [
        '/admin-monitor',
        '/login',
    ],
    /**
     * If true request data will not be logged and will be replaced with
     * ['%__ALL_HIDDEN__%']
     */
    'IN_DB_MONITOR_NEGLICT_REQUEST_DATA' => env('IN_DB_MONITOR_NEGLICT_REQUEST_DATA', false),
    /**
     * Fields in the request which contain any of these names will not be logged 
     * and it will be replaced with %_HIDDEN_%
     * e.x. verified_password
     */
    'IN_DB_MONITOR_NEGLICT_PARAMS_CONTAIN' => [
        'password',
        'pass_word',
        'username',
        'user_name',
        'creditcard',
        'credit_card',
    ],
    /**
     * If true session data will not be logged and will be replaced with
     * ['%__ALL_HIDDEN__%']
     * and will log the session id only  
     */
    'IN_DB_MONITOR_NEGLICT_SESSION_DATA' => env('IN_DB_MONITOR_NEGLICT_SESSION_DATA', false),
    /**
     * If true log the package queries in your laravel log system
     * If your log system is fie, you will find the queries log in storage/logs
     */
    'IN_DB_MONITOR_LOG_PACKAGE_QUERIES' => env('IN_DB_MONITOR_LOG_PACKAGE_QUERIES', false),
    /**
     * If true it will save IP information like country, city
     */
    'IN_DB_MONITOR_GET_IP_INFO' => env('IN_DB_MONITOR_GET_IP_INFO', true),
    /**
     * Refer to the class with get ip info webservices
     */
    'IN_DB_MONITOR_GET_IP_CLASS' => '\\ASamir\\InDbPerformanceMonitor\\IPInfo',
];
