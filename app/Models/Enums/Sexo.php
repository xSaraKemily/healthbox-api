<?php

namespace App\Models\Enums;

class Sexo
{
    public function sexos($sexo = null)
    {
        $sexos = [
            'feminino'  => 'Feminino',
            'masculino' => 'Masculino',
            'outros'    => 'Outros'
        ];

        if($sexo) {
            return $sexos[$sexo];
        }

        return $sexos;
    }
}
