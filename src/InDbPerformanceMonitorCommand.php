<?php

namespace ASamir\InDbPerformanceMonitor;

use Illuminate\Console\Command;

class InDbPerformanceMonitorCommand extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'in-db-performance-monitor:init {--ips=false} {--serialize=false}';

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

        $stop = false;
        // Fill IPs table
        if ($this->option('ips') == 'true') {
            \DB::statement('insert into asamir_log_ips(created_at, updated_at, ip, total_c, total_c_error,  is_finished) SELECT min(created_at), max(updated_at), ip, count(*), sum(has_errors), 0 FROM asamir_log_requests group by ip');
            $this->info('Done => Filling IPs table');
            $this->info('Hint => Go to /admin-monitor/ips-report and click "Complete IPs Info." button in order to update the IPs Info.');
            $stop = true;
        }

        // Serialize all json objects
        if ($this->option('serialize') == 'true') {
            // Update requests
            LogRequests::chunk(100, function ($data) {
                foreach ($data as $row) {
                    $row->parameters = serialize(json_decode($row->parameters, true));
                    $row->session_data = serialize(json_decode($row->session_data, true));
                    $row->save();
                }
            });

            // Update queries
            LogQueries::chunk(100, function ($data) {
                foreach ($data as $row) {
                    $row->bindings = serialize(json_decode($row->bindings, true));
                    $row->save();
                }
            });

            $this->info('Done => All json decoded data have been serialized');
            $stop = true;
        }

        if ($stop)
            return;

        //  Publish
        $this->call('vendor:publish', ['--provider' => 'ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorProvider', '--force' => true]);
        $this->info('Done => Publish the package files');
        // Set default passowrd "monitor"
        $content = file_get_contents(config_path('inDbPerformanceMonitor.php'));
        $content = str_replace('%SET_TOKEN%', bcrypt('monitor'), $content);
        file_put_contents(config_path('inDbPerformanceMonitor.php'), $content);
        $this->info('Done => Set package default passowrd to "monitor"');
        // Add env variables
        $conten = "";
        if (file_exists(base_path('.env')))
            $content = file_get_contents(base_path('.env'));
        $append = "\n"
                . "\nIN_DB_MONITOR_WORK=true"
                . "\nIN_DB_MONITOR_PANEL=true"
                . "\nIN_DB_MONITOR_DB_HOST=localhost"
                . "\nIN_DB_MONITOR_DB_PORT=3306"
                . "\nIN_DB_MONITOR_DB_DB="
                . "\nIN_DB_MONITOR_DB_USERNAME="
                . "\nIN_DB_MONITOR_DB_PASSWORD="
                . "\nIN_DB_MONITOR_LOG_PACKAGE_QUERIES=false"
                . "\nIN_DB_MONITOR_NEGLICT_REQUEST_DATA=false"
                . "\nIN_DB_MONITOR_NEGLICT_SESSION_DATA=false"
                . "\IN_DB_MONITOR_GET_IP_INFO=true";
        file_put_contents(base_path('.env'), $content . $append);
        $this->info('Done => Append .env file with the package variables');

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
