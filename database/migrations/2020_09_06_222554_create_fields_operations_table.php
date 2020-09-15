<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFieldsOperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ld_fields_operations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('field_id');
            $table->unsignedBigInteger('operation_id');
            $table->dateTime('requested_at');

            $table->foreign('field_id')->references('id')->on('ld_fields');
            $table->foreign('operation_id')->references('id')->on('ld_operations');

            $table->index(['field_id', 'requested_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ld_fields_operations');
    }
}
