<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperationsTable extends Migration
{
    public function up()
    {
        Schema::create('ld_operations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('schema_id');
            $table->string('name');
            $table->timestamps();

            $table->foreign('schema_id')->references('id')->on('ld_schemas');
            $table->index(['schema_id', 'name']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ld_operations');
    }
}
