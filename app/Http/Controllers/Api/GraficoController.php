<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\RemedioTratamento;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class GraficoController extends Controller
{
    /**
     * Pacientes x remédio.
     * grafico de linha horizontal
     */
   public function pacienteRemedio(Request $request)
   {
       $query = RemedioTratamento::select('remedios_tratamentos.remedio_id', 'opinioes.paciente_id', DB::raw("CONCAT(remedios.nome, ' (', remedios.fabricante, ') ') as remedio"))
           ->join('tratamentos', 'tratamentos.id', 'remedios_tratamentos.tratamento_id')
           ->join('opinioes', 'opinioes.id', 'tratamentos.opiniao_id')
           ->join('remedios', 'remedios.id', 'remedios_tratamentos.remedio_id');

       if($request->filled('remedios')) {
           $remedios = $request->remedios;
           if(!is_array($remedios)) {
               $remedios = explode(',', $remedios);
           }
           $query = $query->whereIn('remedios.id', $remedios);
       }

       $query = $query->groupBy('opinioes.paciente_id', 'remedios_tratamentos.remedio_id', 'remedios.nome', 'remedios.fabricante')
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
       $count = 0;
       foreach ($remedios as $key => $m) {
           $data[] = [
               'id'    => $count++, //id ficticio para colocar cor no design do app
               'eixoY' => $m['eixoY'],
               'eixoX' => $key
           ];
       }

       return Response::json($data);
   }

    /**
     * Remédio x eficaz x ineficaz
     * grafico de linha horizontal dupla
     */
    public function remedioEficacia(Request $request)
    {
        $query = RemedioTratamento::select('opinioes.eficaz', 'opinioes.paciente_id', DB::raw("CONCAT(remedios.nome, ' (', remedios.fabricante, ') ') as remedio"))
            ->join('tratamentos', 'tratamentos.id', 'remedios_tratamentos.tratamento_id')
            ->join('opinioes', 'opinioes.id', 'tratamentos.opiniao_id')
            ->join('remedios', 'remedios.id', 'remedios_tratamentos.remedio_id');

        if($request->filled('remedios')) {
            $remedios = $request->remedios;
            if(!is_array($remedios)) {
                $remedios = explode(',', $remedios);
            }

            $query = $query->whereIn('remedios.id', $remedios);
        }

        $query = $query->groupBy('opinioes.paciente_id', 'opinioes.eficaz', 'remedios.nome', 'remedios.fabricante')
            ->get();

        $remedios = [];
        foreach ($query as $dados) {
            $criarIndice = false;

            if(!isset($remedios[$dados->remedio])) {
                $criarIndice = true;
            }

            if($dados->eficaz) {
                $remedios[$dados->remedio]['eficaz'] = $criarIndice ? 1 : +1;
            } else {
                $remedios[$dados->remedio]['ineficaz'] = $criarIndice ? 1 : +1;
            }
        }

        $data = [];
        $count = 0;
        foreach ($remedios as $key => $m) {
            $data[] = [
                'id'             => $count++, //id ficticio para colocar cor no design do app
                'eixoY_eficaz'   => isset($m['eficaz']) ? $m['eficaz'] : 0,
                'eixoY_ineficaz' => isset($m['ineficaz']) ? $m['ineficaz'] : 0,
                'eixoX'          => $key
            ];
        }

        return Response::json($data);
    }
}
