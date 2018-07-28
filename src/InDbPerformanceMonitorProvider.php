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

        if (config('inDbPerformanceMonitor.IN_DB_MONITOR_LOG_PACKAGE_QUERIES'))
            \DB::listen(function ($query) {
                if ($query->connectionName == 'inDbMonitorConn')
                    \Log::info('[' . $query->connectionName . '] ' . $query->sql . ' => [' . implode(', ', $query->bindings) . '] => ' . $query->time);
            });
    }

    /**
     * Run after package installed via composer
     */
    public static function postPackageInstall() {
        // Auto Configure
//        if (file_exists(config_path('inDbPerformanceMonitor.php')) && config('inDbPerformanceMonitor.IN_DB_MONITOR_TOKEN') == '%SET_TOKEN%') {}
        // Set default passowrd "monitor"
        $content = file_get_contents(config_path('inDbPerformanceMonitor.php'));
        $content = str_replace('%SET_TOKEN%', bcrypt('monitor'), $content);
        file_put_contents(config_path('inDbPerformanceMonitor.php'), $content);
        // Add env variables
        if (file_exists(base_path('.env'))) {
            $content = file_get_contents(base_path('.env'));
            $append = "\n\nIN_DB_MONITOR_WORK=true"
                    . "\n\nIN_DB_MONITOR_DB_HOST=localhost"
                    . "\n\nIN_DB_MONITOR_DB_PORT=3306"
                    . "\n\nIN_DB_MONITOR_DB_DB="
                    . "\n\nIN_DB_MONITOR_DB_USERNAME="
                    . "\n\nIN_DB_MONITOR_DB_PASSWORD="
                    . "\n\nIN_DB_MONITOR_LOG_PACKAGE_QUERIES=false";
            file_put_contents(base_path('.env'), $content . $append);
        }
    }
}
