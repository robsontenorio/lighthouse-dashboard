<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Schema as AppSchema;

class CreateSchemasTable extends Migration
{
    public function up()
    {
        Schema::connection($this->connection())
            ->create('ld_schemas', function (Blueprint $table) {
                $table->id();
                $table->string("name");
                $table->string("hash")->nullable();
                $table->text("schema")->nullable();
                $table->timestamps();
            });

        AppSchema::create([
            'name' => config('app.name')
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('ld_schemas');
    }

    public function connection()
    {
        return config('lighthouse-dashboard.connection');
    }
}
