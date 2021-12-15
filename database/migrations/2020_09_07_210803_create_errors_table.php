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
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->string('category');
            $table->text('message');
            $table->text('original_exception')->nullable();
            $table->text('body');
            $table->timestampTz('created_at');

            // Timescaledb does not support FK to hypertables
            // So, creating manually a index
            $table->index(['request_id']);
        });

        // When testing ignore hypertable settings
        if (config('app.env') === 'testing') {
            return;
        }

        // ID Needed for Eloquent relationships
        DB::statement("ALTER TABLE ld_errors DROP COLUMN id");
        DB::statement("ALTER TABLE ld_errors ADD COLUMN id SERIAL");
        DB::statement("CREATE INDEX ld_errors_id_index ON ld_errors USING btree (id)");

        // Create hyper table
        DB::statement("SELECT create_hypertable('ld_errors', 'created_at')");
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
