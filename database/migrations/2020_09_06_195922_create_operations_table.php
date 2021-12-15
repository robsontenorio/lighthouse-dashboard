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
            $table->foreignId('field_id')->constrained('ld_fields');
            $table->timestamps();

            $table->index(['field_id']);
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
