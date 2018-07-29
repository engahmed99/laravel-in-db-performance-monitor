<?php

Route::middleware(['web'])->group(function () {
    Route::prefix('admin-monitor')->group(function () {
        Route::get('requests', 'ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorController@getRequests');
        Route::get('request/{id}', 'ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorController@showRequest');
        Route::get('run-query/{id}', 'ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorController@runQuery');
        Route::get('archive-requests', 'ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorController@archiveRequests');
        Route::get('statistics-report', 'ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorController@statisticsReport');
        Route::get('errors-report', 'ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorController@errorsReport');
        route::get('/', 'ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorController@index');
        route::post('/', 'ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorController@index');
        route::get('logout', 'ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorController@logout');
        route::get('changePassword', 'ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorController@changePassword');
        route::post('changePassword', 'ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorController@changePassword');
        route::get('test', function() {
            
        });
    });
});

