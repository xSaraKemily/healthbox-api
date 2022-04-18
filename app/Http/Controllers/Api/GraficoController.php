<?php

namespace App\Http\Controllers\Api;

use App\Actions\ValidaEstadoAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\AcompanhamentoRequest;
use App\Http\Requests\EspecializacaoRequest;
use App\Http\Requests\MedicoCrmEspecializacaoRequest;
use App\Http\Requests\MedicoCrmRequest;
use App\Models\Acompanhamento;
use App\Models\Especializacao;
use App\Models\MedicoCrm;
use App\Models\MedicoCrmEspecializacao;
use App\Models\RemedioTratamento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
