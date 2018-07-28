# Laravel In DB Performance Monitor
Monitor your laravel application performance by logging requests in your database then analyze it. The log includes request parameters, actions, SQL queries and errors beside that you can know the requests with raw SQL queries.

### Requirements
    Laravel >=5.0

### Installation

1- Run `composer require asamir/laravel-in-db-performance-monitor`

2- Run `php artisan in-db-performance-monitor:init`

3- Add and configure the **inDbMonitorConn** connection in **config/database.php file**

    'connections' => [
        //...
		'inDbMonitorConn' => [
            'driver' => 'mysql',
            'host' => env('IN_DB_MONITOR_DB_HOST', ''),
            'port' => env('IN_DB_MONITOR_DB_PORT', '3306'),
            'database' => env('IN_DB_MONITOR_DB_DB', ''),
            'username' => env('IN_DB_MONITOR_DB_USERNAME', ''),
            'password' => env('IN_DB_MONITOR_DB_PASSWORD', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => 'asamir_',
            'strict' => false,
            'engine' => null,
        ],
	]

4- Add the this middleware in **app/Http/Kernel.php** file 

    protected $middleware = [
        //...
        \ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorMiddleware::class
    ];

5- Add this line in **app/Exceptions/Handler.php** => **public function report(Exception $exception)**

    //...
    \ASamir\InDbPerformanceMonitor\LogErrors::inDbLogError($exception);

6- Run `php artisan migrate`

7- For **laravel < 5.5** add this provider in **config/app.php**

	'providers' => [
		\\...
		\ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorProvider::class,
	]

## Documentation
### Configurations

- The **inDbMonitorConn** connection is where the requests logs will be set, so it can be isolated in another database away from the application database or you can set it in the same database no problem.

- The package creates **inDbPerformanceMonitor.php** file in your config folder which has options
	- **IN\_DB\_MONITOR\_WORK** => If true the package will work and log the comming requests (default = true)
	
	- **IN\_DB\_MONITOR\_TOKEN** => Holds the admin-monitor passowrd token (default password = monitor)

	- **IN\_DB\_MONITOR\_NEGLICT\_START\_WITH** => Array of routes to neglict from log (e.x. /test so any request start with /test will not be  logged in the DB)
	
	- **IN\_DB\_MONITOR\_LOG\_PACKAGE\_QUERIES** => If true log queries made by the package in your laravel log (default = false)
	
- **Hint:** You will find the package env variables created in your .env file
 

	











