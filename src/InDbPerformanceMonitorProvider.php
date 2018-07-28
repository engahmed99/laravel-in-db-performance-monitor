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
            __DIR__ . '/inDbPerformanceMonitor.php' => config_path('inDbPerformanceMonitor.php'),
        ]);
        // Set default passowrd "monitor"
        if (file_exists(config_path('inDbPerformanceMonitor.php')) && config('inDbPerformanceMonitor.IN_DB_MONITOR_TOKEN') == '%SET_TOKEN%') {
            $content = file_get_contents(config_path('inDbPerformanceMonitor.php'));
            $content = str_replace('%SET_TOKEN%', bcrypt('monitor'), $content);
            file_put_contents(config_path('inDbPerformanceMonitor.php'), $content);
        }
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

        if (config('inDbPerformanceMonitor.IN_DB_MONITOR_LOG_PACKAGE_QUERIES'))
            \DB::listen(function ($query) {
                if ($query->connectionName == 'inDbMonitorConn')
                    \Log::info('[' . $query->connectionName . '] ' . $query->sql . ' => [' . implode(', ', $query->bindings) . '] => ' . $query->time);
            });
    }

}
