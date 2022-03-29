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
        $tables = [
            'users',
            'solicitacoes_vinculos',
            'caracteristicas_paciente',
            'especializacoes',
            'caracteristicas_medico',
            'questionarios',
            'acompanhamentos',
            'opinioes',
            'tratamentos',
            'likes',
            'remedios',
            'remedios_tratamentos',
            'questoes',
            'opcoes_questoes',
            'questoes_questionarios',
            'questoes_questionarios_respostas',
            'medicos_crm',
            'medico_crm_especializacoes'
        ];

        foreach ($tables as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
