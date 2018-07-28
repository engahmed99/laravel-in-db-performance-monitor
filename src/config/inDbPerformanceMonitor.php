<?php

return [
    'IN_DB_MONITOR_WORK' => env('IN_DB_MONITOR_WORK', true),
    'IN_DB_MONITOR_TOKEN' => '%SET_TOKEN%',
    'IN_DB_MONITOR_NEGLICT_START_WITH' => [
        '/admin-monitor'
    ],
    'IN_DB_MONITOR_LOG_PACKAGE_QUERIES' => env('IN_DB_MONITOR_LOG_PACKAGE_QUERIES', false),
];
