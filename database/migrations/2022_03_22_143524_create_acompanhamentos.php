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
        Schema::create('acompanhamentos', function (Blueprint $table) {
            $table->id();
            $table->text('descricao_paciente')->nullable();
            $table->integer('quantidade_periodicidade');
            $table->integer('dias_duracao');
            $table->date('data_inicio')->nullable();
            $table->unsignedBigInteger('medico_id');
            $table->unsignedBigInteger('paciente_id');
            $table->unsignedBigInteger('questionario_id')->nullable();
            $table->tinyInteger('ativo')->default(1);
            $table->timestamps();

            $table->index('paciente_id');
            $table->index('medico_id');
            $table->index(['medico_id', 'paciente_id']);
            $table->index('ativo');

            $table->foreign('medico_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('paciente_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('questionario_id')->references('id')->on('questionarios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acompanhamentos');
    }
};
