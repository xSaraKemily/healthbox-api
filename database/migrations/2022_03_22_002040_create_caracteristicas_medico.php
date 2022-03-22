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
            $table->char('estado', 2);
            $table->string('descricao');
            $table->unsignedBigInteger('especializacao_id');
            $table->timestamps();

            $table->index('especializacao_id');
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
