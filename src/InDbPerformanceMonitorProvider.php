<?php

namespace ASamir\InDbPerformanceMonitor;

use Illuminate\Support\ServiceProvider;

class InDbPerformanceMonitorProvider extends ServiceProvider {

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() {
        //
        include __DIR__ . '/routes.php';
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
        $this->publishes([
            __DIR__ . '/config/inDbPerformanceMonitor.php' => config_path('inDbPerformanceMonitor.php'),
            __DIR__ . '/assets/inDbPerformanceMonitor.css' => public_path('css/inDbPerformanceMonitor.css')
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register() {
        //
        $this->app->make('ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorController');
        $this->app->make('ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorMiddleware');
        $this->loadViewsFrom(__DIR__ . '/views/inDbPerformanceMonitor', 'inDbPerformanceMonitor');

        // Case Log Package Queries
        if (config('inDbPerformanceMonitor.IN_DB_MONITOR_LOG_PACKAGE_QUERIES'))
            \DB::listen(function ($query) {
                if ($query->connectionName == 'inDbMonitorConn')
                    \Log::info('[' . $query->connectionName . '] ' . $query->sql . ' => [' . implode(', ', $query->bindings) . '] => ' . $query->time);
            });

        $this->commands(
                'ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorCommand'
        );
    }

}
