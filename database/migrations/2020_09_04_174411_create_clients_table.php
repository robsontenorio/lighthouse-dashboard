<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Client;

class CreateClientsTable extends Migration
{
    public function up()
    {
        Schema::create('ld_clients', function (Blueprint $table) {
            $table->id();
            $table->string("username");
            $table->timestamps();

            $table->index(['username']);
        });

        Client::create([
            'username' => 'anonymous'
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('ld_clients');
    }

    public function getConnection()
    {
        return config('lighthouse-dashboard.connection');
    }
}
