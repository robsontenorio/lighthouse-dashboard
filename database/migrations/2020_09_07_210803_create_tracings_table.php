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
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->text('payload');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->unsignedBigInteger('duration');
            $table->timestampTz('requested_at');

            // Timescaledb does not support FK to hypertables
            // So, creating manually a index
            $table->index(['request_id']);
        });

        // When testing ignore hypertable settings
        if (config('app.env') === 'testing') {
            return;
        }

        // ID Needed for Eloquent relationships
        DB::statement("ALTER TABLE ld_tracings DROP COLUMN id");
        DB::statement("ALTER TABLE ld_tracings ADD COLUMN id SERIAL");
        DB::statement("CREATE INDEX ld_tracings_id_index ON ld_tracings USING btree (id)");

        // Create hyper table
        DB::statement("SELECT create_hypertable('ld_tracings', 'requested_at')");

        // Set compress configuration
        DB::statement("
                ALTER TABLE ld_tracings set (
                    timescaledb.compress,
                    timescaledb.compress_segmentby = 'request_id',
                    timescaledb.compress_orderby = 'requested_at DESC'
                )
        ");

        // Apply compress policy
        DB::statement("SELECT add_compression_policy('ld_tracings', INTERVAL '1 year')");
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
