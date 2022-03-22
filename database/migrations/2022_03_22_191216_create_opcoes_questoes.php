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
        Schema::create('opcoes_questoes', function (Blueprint $table) {
            $table->id();
            $table->text('descricao');
            $table->unsignedBigInteger('questao_id');
            $table->timestamps();

            $table->index('questao_id');

            $table->foreign('questao_id')->references('id')->on('questoes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('opcoes_questoes');
    }
};
