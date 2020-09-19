<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTracingSTable extends Migration
{
    public function up()
    {
        Schema::create('ld_tracings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->json('request');
            $table->timestampTz('start_time');
            $table->timestampTz('end_time');
            $table->unsignedBigInteger('duration');
            $table->json('execution');
            $table->timestamps();

            $table->foreign('request_id')->references('id')->on('ld_requests');
        });
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
