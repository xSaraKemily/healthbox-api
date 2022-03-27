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
        Schema::create('medicos_especializacoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medico_id');
            $table->unsignedBigInteger('especializacao_id');
            $table->timestamps();

            $table->index('medico_id');

            $table->unique(['medico_id', 'especializacao_id']);

            $table->foreign('medico_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('especializacao_id')->references('id')->on('especializacoes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medico_especializacoes');
    }
};
