<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFieldsTable extends Migration
{
    public function up()
    {
        Schema::create('ld_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_id')->constrained('ld_types');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('type_def')->nullable();
            $table->text('args')->nullable();
            $table->timestamps();

            $table->index(['type_id']);
            $table->index(['type_id', 'name']);
            $table->index(['type_id', 'name', 'type_def']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ld_fields');
    }

    public function getConnection()
    {
        return config('lighthouse-dashboard.connection');
    }
}
