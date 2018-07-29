<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogRequestsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('inDbMonitorConn')->create('log_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('action', 500)->nullable();
            $table->string('parameters', 4000)->nullable();
            $table->string('type', 100)->nullable();
            $table->string('url', 500)->nullable();
            $table->string('route_uri', 500)->nullable();
            $table->string('route_static_prefix', 500)->nullable();
            $table->string('session_id', 100)->nullable();
            $table->string('session_data', 4000)->nullable();
            $table->string('ip', 100)->nullable();
            $table->float('queries_total_time')->default(0)->nullable();
            $table->integer('queries_total_count')->default(0)->nullable();
            $table->integer('queries_not_elequent_count')->default(0)->nullable();
            $table->float('exec_time')->nullable();
            $table->tinyInteger('has_errors')->default(0)->nullable();
            $table->tinyInteger('is_json_response')->default(0)->nullable();
            $table->string('archive_tag', 20)->default('0')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('inDbMonitorConn')->dropIfExists('log_requests');
    }

}
