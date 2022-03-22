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
        Schema::create('caracteristicas_medico', function (Blueprint $table) {
            $table->id();
            $table->string('crm', 20);
            $table->char('estado_sigla', 2);
            $table->text('descricao')->nullable();
            $table->unsignedBigInteger('medico_id');
            $table->unsignedBigInteger('especializacao_id')->nullable();
            $table->timestamps();

            $table->index('especializacao_id');
            $table->index('estado_sigla');

            $table->foreign('medico_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('especializacao_id')->references('id')->on('especializacoes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('caracteristicas_medico');
    }
};
