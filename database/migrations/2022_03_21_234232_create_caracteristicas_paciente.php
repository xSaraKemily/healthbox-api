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
        Schema::create('caracteristicas_paciente', function (Blueprint $table) {
            $table->id();
            $table->char('cpf', 11);
            $table->float('peso');
            $table->float('altura');
            $table->enum('sexo', ['feminino', 'masculino', 'outros']);
            $table->unsignedBigInteger('paciente_id');
            $table->timestamps();

            $table->index('paciente_id');
            $table->index('sexo');
            $table->index('peso');
            $table->index('altura');
            $table->index(['altura', 'peso', 'sexo']);

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
        Schema::dropIfExists('caracteristicas_paciente');
    }
};
