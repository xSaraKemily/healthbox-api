<?php

namespace App\Http\Controllers\Api;

use App\Models\Acompanhamento;
use App\Models\QuestaoQuestionario;
use App\Models\QuestaoQuestionarioResposta;
use App\Models\Questionario;
use App\Utils\Functions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\RemedioTratamento;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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

    /**
     * questionarios respondidos x paciente.
     * grafico pie
     */
    public function respostaPaciente(Request $request)
    {
        if(auth()->user()->tipo == 'M' && !$request->filled('paciente_id')) {
            return Response::json('Quando o usuário for médico, o paciente_id é obrigatório', 422);
        }

        $columns = Functions::getColumnsWhere();

        $acompanhamentos = Acompanhamento::where($columns->colunaUser, auth()->user()->id);

        if(auth()->user()->tipo == 'M') {
           $acompanhamentos = $acompanhamentos->where('paciente_id', $request->paciente_id);
        }

        $acompanhamentos = $acompanhamentos->get();

        $pendente   = 0;
        $respondida = 0;
        foreach ($acompanhamentos as $acompanhamento) {
            $datasRespostas   = [];
            $dataAnterior     = Carbon::parse($acompanhamento->data_inicio);
            $datasRespostas[] = $dataAnterior->format('Y-m-d');

            while(count($datasRespostas) < $acompanhamento->dias_duracao) {
                $datasRespostas[] =  $dataAnterior->addDays($acompanhamento->quantidade_periodicidade)->format('Y-m-d');
            }

            $respostas = [];

            if($quest = Questionario::where('acompanhamento_id', $acompanhamento->id)->first()) {
                $questoesIds = $quest->questoes->pluck('id');

                $respostas = QuestaoQuestionarioResposta::whereIn('questionario_questao_id', $questoesIds)
                    ->select(DB::raw('DATE(created_at) as data_resposta'))
                    ->groupBy('data_resposta')
                    ->get()
                    ->pluck('data_resposta')
                    ->toArray();
            }

            foreach ($datasRespostas as $data) {

                if(in_array($data, $respostas)) {
                    $respondida++;
                } else {
                    $pendente++;
                }
            }
        }

        $data = [];
        $total = $respondida + $pendente;

        $data[] = [
            'id'          => 1, //id ficticio para colocar cor no design do app
            'pendentes'   => round(($pendente * 100) / $total, 2),
            'respondidos' => round(($respondida * 100) / $total, 2),
        ];

        return Response::json($data);
    }

    /**
     * Remédio x eficaz x ineficaz
     * grafico de barra horizontal dupla
     */
    public function acompanhamentoOpiniaoEficacia(Request $request)
    {
        $acompanhamentos = Acompanhamento::query();

        if($request->medico_id) {
            $medicosId = $request->medico_id;
            if(!is_array($medicosId)) {
                $medicosId = explode(',', $medicosId);
            }
        }

        if($request->paciente_id) {
            $pacientesId = $request->paciente_id;
            if(!is_array($pacientesId)) {
                $pacientesId = explode(',', $pacientesId);
            }
        }

        $medicoId   = Auth::user()->tipo == 'M' ? [Auth::user()->id]  : null;
        $pacienteId = Auth::user()->tipo == 'P' ? [Auth::user()->id]  : null;

        $whereMedico   = isset($medicosId) ? $medicosId : $medicoId;
        $wherePaciente = isset($pacientesId) ? $pacientesId : $pacienteId;

        if($whereMedico) {
            $acompanhamentos = $acompanhamentos->whereIn('medico_id', $whereMedico);
        }

        if($wherePaciente) {
            $acompanhamentos = $acompanhamentos->whereIn('paciente_id', $wherePaciente);
        }

        $acompanhamentos = $acompanhamentos->get();

        $data = [];
        foreach ($acompanhamentos as $acompanhamento) {
            if(!$acompanhamento->tratamento) {
                continue;
            }

            if(!$acompanhamento->tratamento->remedios) {
                continue;
            }

            $remedios = $acompanhamento->tratamento->remedios->pluck('remedio_id');

            $query = RemedioTratamento::select('remedios_tratamentos.tratamento_id', 'opinioes.eficaz', 'opinioes.id as opiniaoId')
                ->join('tratamentos', 'tratamentos.id', 'remedios_tratamentos.tratamento_id')
                ->join('opinioes', 'opinioes.id', 'tratamentos.opiniao_id')
                ->join('remedios', 'remedios.id', 'remedios_tratamentos.remedio_id')
                ->whereIn('remedios.id', $remedios)
                ->groupBy('opinioes.id')
                ->get();

            foreach ($query as $opiniao) {
                $remediosOpiniao = $opiniao->tratamento->remedios->pluck('remedio_id');

                $count = 0;
                foreach ($remediosOpiniao as $remOp) {
                    if(in_array($remOp, $remedios->toArray())) {
                        $count++;
                    }
                }

                if(count($remediosOpiniao) <> count($remedios) || $count <> count($remedios)){
                    continue 2;
                }

                $data[] = (object)['opiniao_id' => $opiniao->opiniaoId, 'eficaz' => $opiniao->eficaz];
            }
        }

        $totalEficaz = 0;
        $totalIneficaz = 0;
        foreach ($data as  $dt) {
            switch ($dt->eficaz) {
                case 1: $totalEficaz++;
                    break;
                case 0: $totalIneficaz++;
                    break;
            }
        }

        $total = $totalEficaz + $totalIneficaz;

        $dtGrafico[] = [
            'id'         => 1, //id ficticio para colocar cor no design do app
            'eficaz'     => round(($totalEficaz * 100) / $total, 2),
            'ineficaz'   => round(($totalIneficaz * 100) / $total, 2),
        ];

        return Response::json($dtGrafico);
    }

    public static function getAcompanhamentosQuery(Request $request, $filtrarExercicio = false)
    {
        $idPergunta = $filtrarExercicio ? 2 : 1;

        $columns = Functions::getColumnsWhere();

        $query = Acompanhamento::join('tratamentos', 'tratamentos.acompanhamento_id', 'acompanhamentos.id')
            ->join('remedios_tratamentos as rt', 'rt.tratamento_id', 'tratamentos.id')
            ->join('remedios', 'remedios.id', 'rt.remedio_id')
            ->join('questionarios', 'questionarios.acompanhamento_id', 'acompanhamentos.id')
            ->join('questoes_questionarios as qq', 'qq.questionario_id', 'questionarios.id')
            ->join('questoes_questionarios_respostas as qr', 'qr.questionario_questao_id', 'qq.id')
            ->where('qq.questao_id', $idPergunta)
            ->where('acompanhamentos.'.$columns->colunaUser, auth()->user()->id)
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
