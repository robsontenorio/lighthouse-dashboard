<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateErrorsTable extends Migration
{
    public function up()
    {
        Schema::create('ld_errors', function (Blueprint $table) {
            $table->foreignId('client_id')->constrained('ld_clients');
            $table->foreignId('operation_id')->constrained('ld_operations');
            $table->string('category');
            $table->text('message');
            $table->text('original_exception')->nullable();
            $table->text('body');
            $table->timestampTz('requested_at');
        });

        // When testing ignore hypertable settings
        if (config('app.env') === 'testing') {
            return;
        }

        // Create hyper table
        DB::statement("SELECT create_hypertable('ld_errors', 'requested_at')");
    }

    public function down()
    {
        Schema::dropIfExists('ld_errors');
    }

    public function getConnection()
    {
        return config('lighthouse-dashboard.connection');
    }
}
