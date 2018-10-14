<?php

Route::group($__middleware, function () {
    Route::get('admin-monitor/dashboard', 'ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorController@dashboard');
    Route::get('admin-monitor/requests', 'ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorController@getRequests');
    Route::get('admin-monitor/request/{id}', 'ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorController@showRequest');
    Route::get('admin-monitor/run-query/{id}', 'ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorController@runQuery');
    Route::post('admin-monitor/archive-requests', 'ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorController@archiveRequests');
    Route::get('admin-monitor/statistics-report', 'ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorController@statisticsReport');
    Route::get('admin-monitor/errors-report', 'ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorController@errorsReport');
    Route::get('admin-monitor/archives-report', 'ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorController@archivesReport');
    Route::delete('admin-monitor/delete-archive', 'ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorController@deleteArchive');
    Route::get('admin-monitor/ips-report', 'ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorController@ipsReport');
    Route::get('admin-monitor', '\ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorController@index');
    Route::post('admin-monitor', '\ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorController@index');
    Route::get('admin-monitor/logout', '\ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorController@logout');
    Route::get('admin-monitor/change-password', 'ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorController@changePassword');
    Route::post('admin-monitor/change-password', 'ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorController@changePassword');
});
