<?php

namespace App\Models\Enums;

class Sexo
{
    public function sexos($sexo = null)
    {
        $sexos = [
            'F'  => 'Feminino',
            'M' => 'Masculino',
            'O' => 'Outros'
        ];

        if($sexo) {
            return $sexos[$sexo];
        }

        return $sexos;
    }
}
