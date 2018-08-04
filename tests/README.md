<!--ts-->
   [Back](https://github.com/engahmed99/laravel-in-db-performance-monitor "https://github.com/engahmed99/laravel-in-db-performance-monitor")
<!--te-->

## Generating Dummy Requests Using Jmeter

#### Dummy Data => [Demo](http://asamirdemos.codeagroup.net/admin-monitor "http://asamirdemos.codeagroup.net/admin-monitor") (password=monitor)

### Setup

 - Create new laravel application >= 5.1.
 - Copy the files in the base_path folder into your laravel base path.
 - In your **config/database.php** create two connections
	
    	'connections' => [
	        /**
			* This is the base connection
			* used for Customers, Products, and Orders models
			*/
			'mysql' => [
	            'driver' => 'mysql',
	            'host' => env('DB_HOST', '127.0.0.1'),
	            'port' => env('DB_PORT', '3306'),
	            'database' => env('DB_DATABASE', 'forge'),
	            'username' => env('DB_USERNAME', 'forge'),
	            'password' => env('DB_PASSWORD', ''),
	            'unix_socket' => env('DB_SOCKET', ''),
	            'charset' => 'utf8mb4',
	            'collation' => 'utf8mb4_unicode_ci',
	            'prefix' => '',
	            'strict' => true,
	            'engine' => null,
	        ],
	
	        /**
			* used for Ads model
			*/
			'mysql' => [
	            'driver' => 'mysql',
	            'host' => env('DB_HOST', '127.0.0.1'),
	            'port' => env('DB_PORT', '3306'),
	            'database' => env('DB_DATABASE', 'forge'),
	            'username' => env('DB_USERNAME', 'forge'),
	            'password' => env('DB_PASSWORD', ''),
	            'unix_socket' => env('DB_SOCKET', ''),
	            'charset' => 'utf8mb4',
	            'collation' => 'utf8mb4_unicode_ci',
	            'prefix' => '',
	            'strict' => true,
	            'engine' => null,
	        ],
		]

	You can give them the same configurations or not, as you like, we use two connections to test the log behavior with multiple queries connections.

- Put this in your routes file
	
		Route::get('customers/generate-errors/{error_code}', 'CustomersController@generateErrors');
		Route::get('customers/generate-json', 'CustomersController@generateJson');
		Route::resource('customers', 'CustomersController');
		Route::resource('products', 'ProductsController');
		Route::resource('orders', 'OrdersController');

- In **app/Http/Middleware/VerifyCsrfToken.php** put these array
	
		protected $except = [
			'customers',
			'customers/*',
			'orders',
			'orders/*',
			'products',
			'products/*',
			'admin-monitor',
		];

	To neglict the CSRF check for the jmeter requests

- Run ` php artisan migrate `
- Open the **Test-Plan-[in-db-performance-monitor].jmx** file by the jmeter.
- Edit the monitor password if changed in the **User Defined Variables** (default = monitor).
- Edit the web server info in the **HTTP Request Defaults** (default = http://localhost:8000)
- At each thread group you can edit the number of threads and number of loops.
- The jmeter reads the data from the csv files in the **.csv** folder

		*_c.csv files => Used for create requests (POST)
		*_e.csv files => Used for edit   requests (PATCH)
		*_d.csv files => Used for delete requests (DELETE)
		*_x.csv files => Used for errors requests
- If you want to generate another csv files use the **DummyCsvCreator in database/seeds** and run ` php artisan db:seed ` after that copy the files from **database/seeds/csv** to **jmeter/csv** folder
- Then run the jmeter and enjoy monitor the request in **http://localhost:8000/admin-monitor/dashboard**


## Author

**Ahmed Samir**

**Contacts:** [eng.ahmed.samir.fci@gmail.com](mailto:eng.ahmed.samir.fci@gmail.com) | [Linkedin](https://www.linkedin.com/in/ahmed-samir-58250284/)

