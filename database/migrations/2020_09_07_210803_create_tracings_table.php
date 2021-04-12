<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTracingsTable extends Migration
{
    public function up()
    {
        Schema::create('ld_tracings', function (Blueprint $table) {
            $table->foreignId('client_id')->constrained('ld_clients');
            $table->foreignId('operation_id')->constrained('ld_operations');
            $table->text('payload');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->unsignedBigInteger('duration');
            $table->json('execution');
            $table->timestampTz('requested_at');
        });

        // When testing ignore hypertable settings
        if (config('app.env') === 'testing') {
            return;
        }

        // Create hyper table
        DB::statement("SELECT create_hypertable('ld_tracings', 'requested_at')");
    }

    public function down()
    {
        Schema::dropIfExists('ld_tracings');
    }

    public function getConnection()
    {
        return config('lighthouse-dashboard.connection');
    }
}
