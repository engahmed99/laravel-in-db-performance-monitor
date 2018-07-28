<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogErrorsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('inDbMonitorConn')->create('log_errors', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('message', 4000)->nullable();
            $table->integer('code')->nullable();
            $table->string('file', 4000)->nullable();
            $table->integer('line')->nullable();
            $table->text('trace')->nullable();
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
        Schema::connection('inDbMonitorConn')->dropIfExists('log_errors');
    }

}
