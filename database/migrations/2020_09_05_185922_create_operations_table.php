<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ld_operations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('schema_id');
            $table->string('name');
            $table->unsignedBigInteger('average_duration')->nullable();
            $table->unsignedBigInteger('latest_duration')->nullable();
            $table->unsignedBigInteger('total_requests')->default(0);
            $table->timestamps();

            $table->foreign('schema_id')->references('id')->on('ld_schemas');
            $table->index(['schema_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ld_operations');
    }
}
