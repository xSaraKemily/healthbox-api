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
        Schema::create('questoes_questionarios_respostas', function (Blueprint $table) {
            $table->id();
            $table->string('resposta_descritiva')->nullable();
            $table->unsignedBigInteger('opcao_id')->nullable();
            $table->unsignedBigInteger('questionario_questao_id');
            $table->timestamps();

            $table->foreign('opcao_id')->references('id')->on('opcoes_questoes')->onDelete('set null');
            $table->foreign('questionario_questao_id')->references('id')->on('questoes_questionarios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questoes_questionarios_respostas');
    }
};
