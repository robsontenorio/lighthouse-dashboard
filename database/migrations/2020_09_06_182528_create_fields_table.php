<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ld_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('type_id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('type_def')->nullable();
            $table->timestamps();

            $table->foreign('type_id')->references('id')->on('ld_types');
            $table->index(['type_id', 'name']);
            $table->index(['type_id', 'name', 'type_def']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ld_fields');
    }
}
