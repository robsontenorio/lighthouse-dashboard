<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Client;

class CreateClientsTable extends Migration
{
    public function up()
    {
        Schema::connection($this->connection())
            ->create('ld_clients', function (Blueprint $table) {
                $table->id();
                $table->string("username");
                $table->timestamps();
            });

        Client::create([
            'username' => 'anonymous_client'
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('ld_clients');
    }

    public function connection()
    {
        return config('lighthouse-dashboard.connection');
    }
}
