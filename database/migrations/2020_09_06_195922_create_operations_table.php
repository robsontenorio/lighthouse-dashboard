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
            $table->unsignedBigInteger('field_id');
            $table->timestamps();

            $table->foreign('field_id')->references('id')->on('ld_fields');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ld_operations');
    }

    public function getConnection()
    {
        return config('lighthouse-dashboard.connection');
    }
}
