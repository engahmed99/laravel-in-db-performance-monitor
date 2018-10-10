<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogIPsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('inDbMonitorConn')->create('log_ips', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('ip')->unique()->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->char('country', 3)->nullable();
            $table->string('country_name')->nullable();
            $table->string('hostname')->nullable();
            $table->string('loc')->nullable();
            $table->string('org')->nullable();
            $table->integer('total_c')->default(0)->nullable();
            $table->integer('total_c_error')->default(0)->nullable();
            $table->tinyInteger('is_finished')->default(0)->nullable();
            //
            $table->index(['country']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('inDbMonitorConn')->dropIfExists('log_ips');
    }

}
