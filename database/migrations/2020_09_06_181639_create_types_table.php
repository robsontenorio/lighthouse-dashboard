<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTypesTable extends Migration
{
    public function up()
    {
        Schema::create('ld_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('schema_id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();

            $table->foreign('schema_id')->references('id')->on('ld_schemas');
            $table->index(['name']);
            $table->index(['schema_id', 'name']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ld_types');
    }

    public function getConnection()
    {
        return config('lighthouse-dashboard.connection');
    }
}
