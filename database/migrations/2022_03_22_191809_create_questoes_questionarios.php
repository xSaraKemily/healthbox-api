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
        Schema::create('questoes_questionarios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('questionario_id');
            $table->unsignedBigInteger('questao_id');
            $table->timestamps();

            $table->index('questionario_id');
            $table->index(['questionario_id', 'questao_id']);

            $table->foreign('questionario_id')->references('id')->on('questionarios')->onDelete('cascade');
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
        Schema::dropIfExists('questoes_questionarios');
    }
};
