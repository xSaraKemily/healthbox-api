<?php

namespace  App\Models\Enums;

class UnidadeMedidaRemedio
{
    public function unidadesMedidas($unSigla = null)
    {
        $unidades = [
          'MG'  => 'Miligramas',
          'G'   => 'Gramas',
          'ML'  => 'Mililitros',
          'GO'  => 'Gotas',
          'MGO' => 'Microgotas',
        ];

        if($unSigla) {
            return $unidades[$unSigla];
        }

        return $unidades;
    }
}
