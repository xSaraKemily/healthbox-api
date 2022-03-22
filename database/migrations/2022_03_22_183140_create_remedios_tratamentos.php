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
        Schema::create('remedios_tratamentos', function (Blueprint $table) {
            $table->id();
            $table->float('dose');
            $table->string('unidade_medida');
            $table->integer('duracao'); //dias
            $table->integer('intervalo');
            $table->enum('periodicidade', ['horas', 'dias']);
            $table->unsignedBigInteger('remedio_id');
            $table->unsignedBigInteger('tratamento_id');
            $table->timestamps();

            $table->index('remedio_id');
            $table->index('tratamento_id');

            $table->foreign('remedio_id')->references('id')->on('remedios')->onDelete('cascade');
            $table->foreign('tratamento_id')->references('id')->on('tratamentos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('remedios_tratamentos');
    }
};
