<?php

namespace ASamir\InDbPerformanceMonitor;

use Illuminate\Console\Command;

class InDbPerformanceMonitorCommand extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'in-db-performance-monitor:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize asamir/laravel-in-db-performance-monitor package';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        //  Publish
        $this->call('vendor:publish', ['--provider' => 'ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorProvider', '--force' => true]);
        $this->info('Done => Publish the package files');
        // Set default passowrd "monitor"
        $content = file_get_contents(config_path('inDbPerformanceMonitor.php'));
        $content = str_replace('%SET_TOKEN%', bcrypt('monitor'), $content);
        file_put_contents(config_path('inDbPerformanceMonitor.php'), $content);
        $this->info('Done => Set package default passowrd to "monitor"');
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
            $this->info('Done => Append .env file with the package variables');
        }
        $this->info("---------");
        $this->info("--------- Remember ---------");
        $this->info("---------");
        $this->info('- Add and configure the "inDbMonitorConn" connection in config/database.php file connections array.');
        $this->info('- Add the "\ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorMiddleware::class" middleware in app/Http/Kernel.php file protected $middleware array.');
        $this->info('- Add the "\ASamir\InDbPerformanceMonitor\LogErrors::inDbLogError($exception);" line in app/Exceptions/Handler.php');
        $this->info('- Run php artisan migrate');
        $this->info('- For laravel<5.5 add the provider "\ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorProvider" in config/app.php file providers array.');
        $this->info("--------- Done ---------");
    }

}
