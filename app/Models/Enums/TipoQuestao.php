<?php

namespace  App\Models\Enums;

class TipoQuestao
{
    public function tipos($tipoSigla = null)
    {
        $tipos = [
          'O' => 'Objetiva',
          'D' => 'Dissertativa'
        ];

        if($tipoSigla) {
            return $tipos[$tipoSigla];
        }

        return $tipos;
    }
}
