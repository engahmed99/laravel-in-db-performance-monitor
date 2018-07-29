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
     * e.x. all routes which starts with /admin-monitor will be neglected from log
     */
    'IN_DB_MONITOR_NEGLICT_START_WITH' => [
        '/admin-monitor'
    ],
    
    /**
     * If true log the package queries in your laravel log system
     * If your log system is fie, you will find the queries log in storage/logs
     */
    'IN_DB_MONITOR_LOG_PACKAGE_QUERIES' => env('IN_DB_MONITOR_LOG_PACKAGE_QUERIES', false),
];
