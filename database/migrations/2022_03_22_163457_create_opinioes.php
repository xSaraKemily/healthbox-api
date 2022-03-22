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
        Schema::create('opinioes', function (Blueprint $table) {
            $table->id();
            $table->text('descricao');
            $table->unsignedBigInteger('paciente_id');
            $table->tinyInteger('eficaz');
            $table->tinyInteger('ativo');
            $table->timestamps();

            $table->index('paciente_id');
            $table->index('eficaz');
            $table->index('ativo');
            $table->index(['ativo', 'eficaz']);
            $table->index(['ativo', 'paciente_id']);

            $table->foreign('paciente_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('opinioes');
    }
};
