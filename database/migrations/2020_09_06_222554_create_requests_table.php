<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('ld_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('field_id');
            $table->unsignedBigInteger('operation_id');
            $table->unsignedBigInteger('client_id');
            $table->dateTime('requested_at');
            $table->unsignedBigInteger('duration')->nullable();

            $table->foreign('field_id')->references('id')->on('ld_fields');
            $table->foreign('operation_id')->references('id')->on('ld_operations');
            $table->foreign('client_id')->references('id')->on('ld_clients');

            $table->index(['field_id', 'requested_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ld_requests');
    }
}
