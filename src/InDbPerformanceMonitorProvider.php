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
        // Add routes, migrations, and publish files
        include __DIR__ . '/routes.php';
        $this->publishes([
            __DIR__ . '/config/inDbPerformanceMonitor.php' => config_path('inDbPerformanceMonitor.php'),
            __DIR__ . '/assets/inDbPerformanceMonitor.css' => public_path('css/inDbPerformanceMonitor.css'),
            __DIR__ . '/assets/inDbPerformanceMonitor.js' => public_path('js/inDbPerformanceMonitor.js'),
            __DIR__ . '/migrations/' => database_path('migrations'),
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register() {
        // Add files and views
        $this->loadViewsFrom(__DIR__ . '/views/inDbPerformanceMonitor', 'inDbPerformanceMonitor');

        // Case Log Package Queries
        if (config('inDbPerformanceMonitor.IN_DB_MONITOR_LOG_PACKAGE_QUERIES')) {
            $version = substr(app()->version(), 0, 3);
            if (in_array($version, ['5.0', '5.1']))
                \DB::listen(function ($sql, $bindings, $time, $connectionName) {
                    if ($connectionName == 'inDbMonitorConn')
                        \Log::info('[' . $connectionName . '] ' . $sql . ' => [' . json_encode($bindings) . '] => ' . $time);
                });
            else
                \DB::listen(function ($query) {
                    if ($query->connectionName == 'inDbMonitorConn')
                        \Log::info('[' . $query->connectionName . '] ' . $query->sql . ' => [' . json_encode($query->bindings) . '] => ' . $query->time);
                });
        }

        // Add command
        $this->commands(
                'ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorCommand'
        );
    }

}
