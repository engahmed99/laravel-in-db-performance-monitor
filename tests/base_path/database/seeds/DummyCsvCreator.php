<?php

use Illuminate\Database\Seeder;

//use Faker\Generator as Faker;

class DummyCsvCreator extends Seeder {

    public $customers_count = 500;
    public $products_count = 50;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $this->dummyCustomers($this->customers_count, 100, 50, 50);
        $this->dummyProducts($this->products_count, 20, 10, 10);
        $this->dummyOrders($this->customers_count, 50, 20, 20);
    }

    public function dummyCustomers($count_c, $count_e, $count_d, $count_x) {
        // Initialize
        $faker = Faker\Factory::create();
        $file_c = __DIR__ . '/csv/customers_c.csv';
        $file_e = __DIR__ . '/csv/customers_e.csv';
        $file_d = __DIR__ . '/csv/customers_d.csv';
        $file_x = __DIR__ . '/csv/customers_x.csv';
        $header = [
            'c_id', 'c_name', 'c_email', 'c_mobile', 'c_address', 'c_birth_date', 'c_kids_no'
        ];
        // Set files header 
        file_put_contents($file_c, implode(',', $header) . "\n");
        file_put_contents($file_e, implode(',', $header) . "\n");
        file_put_contents($file_d, "c_id\n");
        file_put_contents($file_x, "c_error_code\n");
        // Open files ro write
        $handle_c = fopen($file_c, 'a') or die('Cannot open file:  ' . $file_c);
        $handle_e = fopen($file_e, 'a') or die('Cannot open file:  ' . $file_e);
        $handle_d = fopen($file_d, 'a') or die('Cannot open file:  ' . $file_d);
        $handle_x = fopen($file_x, 'a') or die('Cannot open file:  ' . $file_x);
        for ($i = 1; $i <= $count_c; $i++) {
            $data = [
                'id' => $i,
                'name' => $this->clear($faker->name),
                'email' => $this->clear($faker->email),
                'mobile' => $faker->phoneNumber,
                'address' => $this->clear($faker->address),
                'birth_date' => $faker->date(),
                'kids_no' => $faker->numberBetween(0, 5)
            ];
            // Create
            $line = implode(',', array_values($data)) . "\n";
            fwrite($handle_c, $line);
            // Edit
            if ($i <= $count_e) {
                $data['name'] = $this->clear($faker->name);
                $line = implode(',', array_values($data)) . "\n";
                fwrite($handle_e, $line);
            }
            // Delete
            if ($i <= $count_d) {
                $line = $data['id'] . "\n";
                fwrite($handle_d, $line);
            }
            // Error
            if ($i <= $count_x) {
                $line = $faker->numberBetween(0, 10) . "\n";
                fwrite($handle_x, $line);
            }
        }
        // Close files
        fclose($handle_c);
        fclose($handle_e);
        fclose($handle_d);
        fclose($handle_x);
    }

    public function dummyProducts($count_c, $count_e, $count_d, $count_x) {
        // Initialize
        $faker = Faker\Factory::create();
        $file_c = __DIR__ . '/csv/products_c.csv';
        $file_e = __DIR__ . '/csv/products_e.csv';
        $file_d = __DIR__ . '/csv/products_d.csv';
        $file_x = __DIR__ . '/csv/products_x.csv';
        $header = [
            'p_id', 'p_name', 'p_company', 'p_price'
        ];
        // Set files header 
        file_put_contents($file_c, implode(',', $header) . "\n");
        file_put_contents($file_e, implode(',', $header) . "\n");
        file_put_contents($file_d, "p_id\n");
        file_put_contents($file_x, "p_error_code\n");
        // Open files ro write
        $handle_c = fopen($file_c, 'a') or die('Cannot open file:  ' . $file_c);
        $handle_e = fopen($file_e, 'a') or die('Cannot open file:  ' . $file_e);
        $handle_d = fopen($file_d, 'a') or die('Cannot open file:  ' . $file_d);
        $handle_x = fopen($file_x, 'a') or die('Cannot open file:  ' . $file_x);
        for ($i = 1; $i <= $count_c; $i++) {
            $data = [
                'id' => $i,
                'name' => $this->clear($faker->word),
                'company' => $this->clear($faker->company),
                'price' => $faker->numberBetween(5, 5000)
            ];
            // Create
            $line = implode(',', array_values($data)) . "\n";
            fwrite($handle_c, $line);
            // Edit
            if ($i <= $count_e) {
                $data['name'] = $this->clear($faker->word);
                $line = implode(',', array_values($data)) . "\n";
                fwrite($handle_e, $line);
            }
            // Delete
            if ($i <= $count_d) {
                $line = $data['id'] . "\n";
                fwrite($handle_d, $line);
            }
            // Error
            if ($i <= $count_x) {
                $line = $faker->numberBetween(0, 10) . "\n";
                fwrite($handle_x, $line);
            }
        }
        // Close files
        fclose($handle_c);
        fclose($handle_e);
        fclose($handle_d);
        fclose($handle_x);
    }

    public function dummyOrders($count_c, $count_e, $count_d, $count_x) {
        // Initialize
        $faker = Faker\Factory::create();
        $file_c = __DIR__ . '/csv/orders_c.csv';
        $file_e = __DIR__ . '/csv/orders_e.csv';
        $file_d = __DIR__ . '/csv/orders_d.csv';
        $file_x = __DIR__ . '/csv/orders_x.csv';
        $header = [
            'o_id', 'o_customer_id', 'o_product_id', 'o_is_sale', 'o_amount'
        ];
        // Set files header 
        file_put_contents($file_c, implode(',', $header) . "\n");
        file_put_contents($file_e, implode(',', $header) . "\n");
        file_put_contents($file_d, "o_id\n");
        file_put_contents($file_x, "o_error_code\n");
        // Open files ro write
        $handle_c = fopen($file_c, 'a') or die('Cannot open file:  ' . $file_c);
        $handle_e = fopen($file_e, 'a') or die('Cannot open file:  ' . $file_e);
        $handle_d = fopen($file_d, 'a') or die('Cannot open file:  ' . $file_d);
        $handle_x = fopen($file_x, 'a') or die('Cannot open file:  ' . $file_x);
        for ($i = 1; $i <= $count_c; $i++) {
            $data = [
                'id' => $i,
                'customer_id' => $faker->numberBetween(1, $this->customers_count),
                'product_id' => $faker->numberBetween(1, $this->products_count),
                'is_sale' => $faker->numberBetween(0, 1),
                'amount' => $faker->numberBetween(1, 20)
            ];
            // Create
            $line = implode(',', array_values($data)) . "\n";
            fwrite($handle_c, $line);
            // Edit
            if ($i <= $count_e) {
                $data['amount'] = $faker->numberBetween(1, 20);
                $line = implode(',', array_values($data)) . "\n";
                fwrite($handle_e, $line);
            }
            // Delete
            if ($i <= $count_d) {
                $line = $data['id'] . "\n";
                fwrite($handle_d, $line);
            }
            // Error
            if ($i <= $count_x) {
                $line = $faker->numberBetween(0, 10) . "\n";
                fwrite($handle_x, $line);
            }
        }
        // Close files
        fclose($handle_c);
        fclose($handle_e);
        fclose($handle_d);
        fclose($handle_x);
    }

    public function clear($str) {
        return trim(str_replace(['"', ',', "\n"], '', $str));
    }

}
