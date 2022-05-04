<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcompanhamentoRequest;
use App\Models\Acompanhamento;
use App\Models\QuestaoQuestionarioResposta;
use App\Models\Questionario;
use App\Models\User;
use App\Utils\Functions;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class AcompanhamentoController extends Controller
{
    public function index(Request $request)
    {
        $columns = Functions::getColumnsWhere();

        $acompanhamentos = Acompanhamento::where($columns->colunaUser, auth()->user()->id)
            ->with(['medico' => function($query) {
                $query->with('crms');
            }])
            ->with(['paciente' => function($query) {
                $query->with(['caracteristica']);
            }])
            ->with(['tratamento' => function($query) {
                $query->with(['remedios' => function($query) {
                    $query->with('remedio');
                }]);
            }])
            ->with(['questionario' => function($query) {
                $query->with(['questoes' => function($query) {
                    $query->with(['questao' => function($query) {
                        $query->with('opcoes');
                    }])
                    ->with(['resposta' => function($query) {
                        $query->whereDate('questoes_questionarios_respostas.created_at', Carbon::now()->format('Y-m-d'));
                    }]);
                }]);
            }]);

        if($request->filled('usuario_id')) {
            $acompanhamentos = $acompanhamentos->where($columns->colunaOposta, $request->usuario_id);
        }

        if($request->filled('ativo')) {
            $acompanhamentos = $acompanhamentos->where('ativo', $request->ativo);
        }

        $acompanhamentos = $acompanhamentos->get();

        foreach ($acompanhamentos as $acompanhamento) {
            $datasRespostas   = [];
            $dataAnterior     = Carbon::parse($acompanhamento->data_inicio);
            $datasRespostas[] = $dataAnterior->format('Y-m-d');

            while(count($datasRespostas) < $acompanhamento->dias_duracao) {
                $datasRespostas[] =  $dataAnterior->addDays($acompanhamento->quantidade_periodicidade)->format('Y-m-d');
            }

            $questionario = Questionario::where('acompanhamento_id', $acompanhamento->id)
                ->join('questoes_questionarios as qq', 'qq.questionario_id', 'questionarios.id')
                ->first();

            $ultimaResposta = null;
            if($questionario) {
                $questoesIds = $questionario->questoes->pluck('id');

                $ultimaResposta = QuestaoQuestionarioResposta::whereIn('questionario_questao_id', $questoesIds)
                    ->orderBy('created_at', 'desc')
                    ->first();
            }

            $dataAtual = Carbon::now()->format('Y-m-d');

            if(in_array($dataAtual, $datasRespostas) && (!$ultimaResposta || ($ultimaResposta && Carbon::now($ultimaResposta->created_at)->format('Y-m-d') < $dataAtual))) {
                $acompanhamento->resposta_pendente = true;
            } else {
                $acompanhamento->resposta_pendente = false;
            }

            if($acompanhamento->medico) {
                $acompanhamento->medico->caracteristica;
            }
        }

       return $acompanhamentos;
    }

    public function show($id)
    {
        $acompanhamento = Acompanhamento::where('id', $id)
            ->with(['medico' => function($query) {
                $query->with('crms');
            }])
            ->with(['paciente' => function($query) {
                $query->with(['caracteristica']);
            }])
            ->with(['tratamento' => function($query) {
                $query->with(['remedios' => function($query) {
                    $query->with('remedio');
                }]);
            }])
            ->with(['questionario' => function($query) {
                $query->with(['questoes' => function($query) {
                    $query->with(['questao' => function($query) {
                        $query->with('opcoes');
                    }])
                        ->with(['resposta' => function($query) {
                            $query->whereDate('questoes_questionarios_respostas.created_at', Carbon::now()->format('Y-m-d'));
                        }]);
                }]);
            }])
            ->first();

        return $acompanhamento;
    }

    /**
     * @param Request $request
     * @return mixed
     *
     * Retorna todos os questionarios e datas que devem ser respondidos
     */
    public function questionariosResponder(Request $request)
    {
        $columns = Functions::getColumnsWhere();

        $acompanhamentos = Acompanhamento::where($columns->colunaUser, auth()->user()->id)
            ->with(['medico', 'paciente']);

        if($request->filled('usuario_id')) {
            $acompanhamentos = $acompanhamentos->where($columns->colunaOposta, $request->usuario_id);
        }

        if($request->filled('acompanhamento_id')) {
            $acompanhamentos = $acompanhamentos->where('id', $request->acompanhamento_id);
        }

        $acompanhamentos = $acompanhamentos->get();

        $questionarios = [];
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
                $pendente = true;

                if(in_array($data, $respostas)) {
                    $pendente = false;
                }

                $questionario = Questionario::where('acompanhamento_id', $acompanhamento->id)
                    ->with(['questoes' => function($query) use($data){
                    $query->with(['questao' => function($query) {
                        $query->with('opcoes');
                    }]);
                    $query->with(['resposta' => function($query) use($data){
                        $query->whereDate('questoes_questionarios_respostas.created_at', $data);
                    }]);
                }])->first();

                $questionario->data_resposta     = $data;
                $questionario->resposta_pendente = $pendente;
                $questionario->titulo            = $acompanhamento->tratamento->titulo;

                if(auth()->user()->tipo == 'P') {
                    $acompanhamento->medico;
                    $acompanhamento->medico->caracteristica;

                    $questionario->usuario_vinculado = $acompanhamento->medico;
                } else {
                    $acompanhamento->paciente;
                    $acompanhamento->paciente->caracteristica;

                    $questionario->usuario_vinculado = $acompanhamento->paciente;
                }

                $questionarios[] = $questionario;
            }
        }

        return $questionarios;
    }

    public function store(AcompanhamentoRequest $request): JsonResponse
    {
        $acompanhamento = new Acompanhamento($request->all());
        $acompanhamento->medico_id = auth()->user()->id;

        DB::beginTransaction();

        try {
            $acompanhamento->save();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao salvar acompanhamento ' . $e);

            return Response::json(['message' => 'Erro ao salvar acompanhamento.'], 500);
        }

        DB::commit();

        return Response::json([
            'message' => 'Acompanhamento salvo com sucesso',
            'acompanhamento' => $acompanhamento,
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $acompanhamento = Acompanhamento::find($id);

        if (!$acompanhamento) {
            return Response::json(['message' => 'Acompanhamento não encontrado.'], 404);
        }

        $acompanhamento->fill($request->all());

        DB::beginTransaction();

        try {
            $validator = Validator::make($acompanhamento->getAttributes(), $acompanhamento->rules());

            if($validator->fails()) {
                return Response::json(['message' => $validator->errors()->all()], 422);
            }

            $acompanhamento->save();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar acompanhamento ' . $e);

            return Response::json(['message' => 'Erro ao atualizar acompanhamento.'], 500);
        }

        DB::commit();

        return Response::json(['message' => 'Acompanhamento atualizado com sucesso']);
    }

    //todo: testar
    public function destroy($id)
    {
        try {
            $acompanhamento = Acompanhamento::find($id);

            if (!$acompanhamento) {
                return Response::json(['message' => 'Acompanhamento não encontrado.'], 404);
            }

            if ($acompanhamento->tratamento) {
                if ($acompanhamento->tratamento->remedios) {
                    $acompanhamento->tratamento->remedios->delete();
                }

                $acompanhamento->tratamento->delete();

                foreach ($acompanhamento->questionario->questoes as $questao) {
                    $questao->resposta->delete();
                    $questao->delete();
                }
            }

        } catch (Exception $e) {
            Log::error('Erro ao deletar CRM ' . $e);

            return Response::json(['message' => 'Erro ao deletar especializaação'], 500);
        }

        return Response::json(['message' => 'Especializaação deletado com sucesso.']);
    }

    /**
     * Retorna todos os usuarios que tem vinculo de acompanhamento com o usuario logado
     */
    public function usuarioVinculo(Request $request)
    {
        $columns = Functions::getColumnsWhere();
        $colunaWith = auth()->user()->tipo == 'P' ? 'medico' : 'paciente';

        $acompanhamentos = Acompanhamento::where($columns->colunaUser, auth()->user()->id)->with($colunaWith)->get();

        $datas = [];
        foreach ($acompanhamentos as $acompanhamento) {
            $datasRespostas = [];
            $dataAnterior   = Carbon::parse($acompanhamento->data_inicio);
            while(count($datasRespostas) < $acompanhamento->dias_duracao) {
                $datasRespostas[] =  $dataAnterior->addDays($acompanhamento->quantidade_periodicidade)->format('Y-m-d');
            }

            $questionario = Questionario::where('acompanhamento_id', $acompanhamento->id)
                ->join('questoes_questionarios as qq', 'qq.questionario_id', 'questionarios.id')
                ->first();

            $ultimaResposta = null;
            if($questionario) {
                $questoesIds = $questionario->questoes->pluck('id');

                $ultimaResposta = QuestaoQuestionarioResposta::whereIn('questionario_questao_id', $questoesIds)
                    ->select('created_at')
                    ->orderBy('created_at', 'desc')
                    ->first();
            }

            $dataAtual = Carbon::now()->format('Y-m-d');

            if(in_array($dataAtual, $datasRespostas) && (!$ultimaResposta || ($ultimaResposta && Carbon::now($ultimaResposta->created_at)->format('Y-m-d') < $dataAtual))) {
                $acompanhamento->$colunaWith->resposta_pendente = true;
            } else {
                $acompanhamento->$colunaWith->resposta_pendente = false;
            }

            $dataRetorno = $acompanhamento->$colunaWith;

            $dataRetorno->caracteristica;
            $dataRetorno->crms;

            $datas[$dataRetorno->id] = $dataRetorno;
        }

        $dataFormatada = [];
        if ($request->filled('nome')) {
            $filtroNome = User::where('name', 'ilike', "%$request->nome%")->pluck('id')->toArray();


            foreach ($datas as $key => $data) {
                if(in_array($key, $filtroNome)){
                    $dataFormatada[] = $data;
                }
            }
        } else {
            foreach ($datas as  $data) {
                $dataFormatada[] = $data;
            }
        }

        return $dataFormatada;
    }
}
