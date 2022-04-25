<?php

namespace App\Utils;

class Functions
{
    public static function getColumnsWhere()
    {
        switch (auth()->user()->tipo) {
            case 'M':
                {
                    $colunaUser = 'medico_id';
                    $colunaOposta = 'paciente_id';
                    $tipoOposto = 'P';
                }
                break;
            case 'P':
                {
                    $colunaUser = 'paciente_id';
                    $colunaOposta = 'medico_id';
                    $tipoOposto = 'M';
                }
                break;
        }

        return (object)['colunaUser' => $colunaUser, 'colunaOposta' => $colunaOposta, 'tipoOposto' => $tipoOposto];
    }

    public static function checkAcompanhamentoAtivo()
    {

    }
}
