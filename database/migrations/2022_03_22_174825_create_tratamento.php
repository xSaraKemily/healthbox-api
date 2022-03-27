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
        Schema::create('tratamento', function (Blueprint $table) {
            $table->id();
            $table->text('descricao');
            $table->unsignedBigInteger('opiniao_id')->nullable();
            $table->unsignedBigInteger('acompanhamento_id')->nullable();
            $table->timestamps();

            $table->index('opiniao_id');
            $table->index('acompanhamento_id');

            $table->foreign('opiniao_id')->references('id')->on('opinioes')->onDelete('cascade');
            $table->foreign('acompanhamento_id')->references('id')->on('acompanhamentos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tratamento');
    }
};
