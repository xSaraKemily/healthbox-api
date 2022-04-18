<?php

namespace App\Http\Controllers\Api;

use App\Models\RemedioTratamento;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class GraficoController extends Controller
{
    /**
     * Número de pacientes que usaram x remédio.
     */
   public function pacienteRemedio()
   {
       $query = RemedioTratamento::select('remedios_tratamentos.remedio_id', 'opinioes.paciente_id', 'remedios.nome as remedio')
           ->join('tratamentos', 'tratamentos.id', 'remedios_tratamentos.tratamento_id')
           ->join('opinioes', 'opinioes.id', 'tratamentos.opiniao_id')
           ->join('remedios', 'remedios.id', 'remedios_tratamentos.remedio_id')
           ->groupBy('opinioes.paciente_id', 'remedios_tratamentos.remedio_id')
           ->get();

       $remedios = [];
       foreach ($query as $dados) {
           if(isset($remedios[$dados->remedio])) {
               $remedios[$dados->remedio]['eixoY']++;

               if(!isset($remedios[$dados->remedio]['eixoY'])) {
                   $remedios[$dados->remedio]['eixoY'][$dados->remedio];
               }
           } else {
               $remedios[$dados->remedio]['eixoY'] = 1;
           }
       }

       $data = [];
       foreach ($remedios as $key => $m) {
           $data[] = [
               'eixoY' => $m['eixoY'],
               'eixoX' => $key
           ];
       }

       return Response::json($data);
   }
}
