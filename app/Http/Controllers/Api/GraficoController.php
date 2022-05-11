<?php

namespace App\Http\Controllers\Api;

use App\Models\Acompanhamento;
use App\Models\QuestaoQuestionario;
use Illuminate\Http\Request;
use App\Models\RemedioTratamento;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class GraficoController extends Controller
{
    /**
     * Pacientes x remédio.
     * grafico de barra horizontal
     */
   public function pacienteRemedio(Request $request)
   {
       if(!$request->filled('remedios')) {
           return Response::json([]);
       }

       $query = RemedioTratamento::select('remedios_tratamentos.remedio_id', 'opinioes.paciente_id', DB::raw("CONCAT(remedios.nome, ' (', remedios.fabricante, ') ') as remedio"))
           ->join('tratamentos', 'tratamentos.id', 'remedios_tratamentos.tratamento_id')
           ->join('opinioes', 'opinioes.id', 'tratamentos.opiniao_id')
           ->join('remedios', 'remedios.id', 'remedios_tratamentos.remedio_id');

       if($request->remedios) {
           $remedios = $request->remedios;
           if(!is_array($remedios)) {
               $remedios = explode(',', $remedios);
           }
           $query = $query->whereIn('remedios.id', $remedios);
       }

       $query = $query->groupBy('opinioes.paciente_id', 'remedios_tratamentos.remedio_id', 'remedios.nome', 'remedios.fabricante')
           ->get();

       $totalUso = $query->count();

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

       $graficoPie = $request->filled('tipoGrafico') && $request->tipoGrafico == 'pie' ? true : false;

       $data = [];
       $count = 1;
       foreach ($remedios as $key => $m) {
           $data[] = [
               'id'    => $count++, //id ficticio para colocar cor no design do app
               'eixoY' => $graficoPie ? round(($m['eixoY'] * 100) / $totalUso, 2) : $m['eixoY'],
               'eixoX' => $key
           ];
       }

       return Response::json($data);
   }

    /**
     * Remédio x eficaz x ineficaz
     * grafico de barra horizontal dupla
     */
    public function remedioEficacia(Request $request)
    {
        if(!$request->filled('remedios')) {
            return Response::json([]);
        }

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

        $query = $query->get();

        $remedios = [];
        foreach ($query as $dados) {
            if(!isset($remedios[$dados->remedio])) {
                $remedios[$dados->remedio] = [
                    'eficaz'   => 0,
                    'ineficaz' => 0
                ];
            }

            if($dados->eficaz) {
                $remedios[$dados->remedio]['eficaz']++;
            } else {
                $remedios[$dados->remedio]['ineficaz']++;
            }
        }

        $data = [];
        $count = 1;
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

    /**
     * Remédio x melhoria dos sintomas
     * grafico de barra horizontal tripla
     */
    public function remedioMelhoraSintoma(Request $request)
    {
        $graficoExercicio = $request->filled('grafico_exercicio') && $request->grafico_exercicio;

        if(!$request->filled('remedios')) {
            return Response::json([]);
        }

       $query = self::getAcompanhamentosQuery($request);

        if($graficoExercicio) {
            $acompanhamentosComExercicio = self::getAcompanhamentosQuery($request)->pluck('id')->toArray();
        }

        $remedios = [];
        foreach ($query as $acompanhamento) {
            if($graficoExercicio && !in_array($acompanhamento->id, $acompanhamentosComExercicio)) {
                continue;
            }

            if(!isset($remedios[$acompanhamento->remedio])) {
                $remedios[$acompanhamento->remedio] = [
                    'melhorou' => 0,
                    'piorou'   => 0,
                    'igual'    => 0
                ];
            }

            switch ($acompanhamento->opcao_id) {
                case 1:  $remedios[$acompanhamento->remedio]['melhorou']++;
                    break;
                case 2:  $remedios[$acompanhamento->remedio]['igual']++;
                    break;
                case 3:  $remedios[$acompanhamento->remedio]['piorou']++;
                    break;
            }
        }

        $data = [];
        $count = 1;
        foreach ($remedios as $key => $m) {
            $data[] = [
                'id'             => $count++, //id ficticio para colocar cor no design do app
                'eixoY_melhorou' => isset($m['melhorou']) ? $m['melhorou'] : 0,
                'eixoY_igual'    => isset($m['igual']) ? $m['igual'] : 0,
                'eixoY_piorou'   => isset($m['piorou']) ? $m['piorou'] : 0,
                'eixoX'          => $key
            ];
        }

        return Response::json($data);
    }

    public static function getAcompanhamentosQuery(Request $request, $filtrarExercicio = false)
    {
        $idPergunta = $filtrarExercicio ? 2 : 1;

        $query = Acompanhamento::join('tratamentos', 'tratamentos.acompanhamento_id', 'acompanhamentos.id')
            ->join('remedios_tratamentos as rt', 'rt.tratamento_id', 'tratamentos.id')
            ->join('remedios', 'remedios.id', 'rt.remedio_id')
            ->join('questionarios', 'questionarios.acompanhamento_id', 'acompanhamentos.id')
            ->join('questoes_questionarios as qq', 'qq.questionario_id', 'questionarios.id')
            ->join('questoes_questionarios_respostas as qr', 'qr.questionario_questao_id', 'qq.id')
            ->where('qq.questao_id', $idPergunta)
            ->select('remedios.nome as remedio', 'qr.opcao_id','acompanhamentos.id', DB::Raw("DATE(qr.created_at) as data_resposta"), 'qq.questao_id');

        if($filtrarExercicio) {
            $query = $query->where('qr.opcao_id', 4);
        }

        if($request->filled('remedios')) {
            $remedios = $request->remedios;
            if(!is_array($remedios)) {
                $remedios = explode(',', $remedios);
            }

            $query = $query->whereIn('remedios.id', $remedios);
        }

        return $query->get();
    }
}
