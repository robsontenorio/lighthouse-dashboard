<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateErrorsTable extends Migration
{
    public function up()
    {
        Schema::create('ld_errors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('ld_requests');
            $table->string('category');
            $table->string('message');
            $table->text('original_exception')->nullable();
            $table->text('body');

            $table->timestamps();
        });
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
