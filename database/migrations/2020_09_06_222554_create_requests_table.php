<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('ld_requests', function (Blueprint $table) {
            $table->foreignId('field_id')->constrained('ld_fields');
            $table->foreignId('client_id')->constrained('ld_clients');
            $table->foreignId('operation_id')->constrained('ld_operations');
            $table->unsignedBigInteger('duration')->nullable();
            $table->timestampTz('requested_at');

            $table->index(['client_id', 'duration', DB::raw('requested_at DESC')]);
            $table->index(['field_id', 'client_id', 'requested_at']);
            $table->index(['operation_id', 'client_id', 'duration', DB::raw('requested_at DESC')]);
        });

        // When testing ignore hypertable settings
        if (config('app.env') === 'testing') {
            return;
        }

        // Crate hyper table
        DB::statement("SELECT create_hypertable('ld_requests', 'requested_at')");

        // Set compress configuration
        DB::statement("
                ALTER TABLE ld_requests  set (
                    timescaledb.compress,
                    timescaledb.compress_segmentby = 'client_id,field_id,operation_id',
                        timescaledb.compress_orderby = 'requested_at DESC'
                )
        ");

        // Apply compress policy
        DB::statement("SELECT add_compression_policy('ld_requests', INTERVAL '30 days')");
    }

    public function down()
    {
        Schema::dropIfExists('ld_requests');
    }

    public function getConnection()
    {
        return config('lighthouse-dashboard.connection');
    }
}
