<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogQueriesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('inDbMonitorConn')->create('log_queries', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('query', 4000)->nullable();
            $table->string('bindings', 4000)->nullable();
            $table->float('time')->nullable();
            $table->string('connection_name', 500)->nullable();
            $table->tinyInteger('is_elequent')->nullable();
            $table->integer('request_id');
            //
            $table->index(['request_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('inDbMonitorConn')->dropIfExists('log_queries');
    }

}
