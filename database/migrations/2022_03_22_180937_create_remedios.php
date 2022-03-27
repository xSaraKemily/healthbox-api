<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('remedios', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('fabricante');
            $table->string('link_bula')->nullable();
            $table->string('api_id');
            $table->timestamps();

            $table->index('api_id');
            $table->index('nome');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('remedios');
    }
};
