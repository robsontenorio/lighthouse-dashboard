<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use LighthouseDashboard\Models\Schema as AppSchema;

class CreateSchemasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ld_schemas', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("hash")->nullable();
            $table->text("schema")->nullable();
            $table->timestamps();
        });

        // TODO
        AppSchema::create([
            'name' => config('app.name')
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ld_schemas');
    }
}
