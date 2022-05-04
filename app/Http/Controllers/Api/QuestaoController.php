<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuestaoQuestionarioRequest;
use App\Http\Requests\QuestaoQuestionarioRespostaRequest;
use App\Http\Requests\QuestaoRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\Acompanhamento;
use App\Models\CaracteristicaMedico;
use App\Models\CaracteristicaPaciente;
use App\Models\MedicoCrm;
use App\Models\MedicoCrmEspecializacao;
use App\Models\OpcaoQuestao;
use App\Models\Questao;
use App\Models\QuestaoQuestionario;
use App\Models\QuestaoQuestionarioResposta;
use App\Models\Questionario;
use App\Models\User;
use App\Utils\Functions;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class QuestaoController extends Controller
{
    public function index()
    {
        return Questao::where('usuario_id', auth()->user()->id)->with('opcoes')->get();
    }

    public function show($id)
    {
        return Questao::where('id', $id)->with('opcoes')->first();
    }

    /**
     * Store a new user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(QuestaoRequest $request) : JsonResponse
    {
        $questao = new Questao($request->all());
        $questao->usuario_id = auth()->user()->id;

        if($questao->tipo == 'O' && !$request->filled('opcoes')) {
            return Response::json(['message' => 'Para questões objetivas é obrigatório informar as opções'], 422);
        }

        DB::beginTransaction();
        try {
            $questao->save();

            if($request->filled('opcoes')) {
                foreach ($request->opcoes as $opcao) {
                    $newOpcao = new OpcaoQuestao($opcao);
                    $newOpcao->questao_id = $questao->id;

                    $validator = Validator::make($newOpcao->getAttributes(), $newOpcao->rules());

                    if($validator->fails()) {
                        DB::rollBack();
                        return Response::json(['message' => $validator->errors()->all()], 422);
                    }

                    $newOpcao->save();
                }
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao salvar questão'. $e);

            return Response::json(['message' => 'Erro ao salvar questão'], 500);
        }

        DB::commit();

       $questao->opcoes;

        return Response::json([
            'message'    => 'Questão salva com sucesso',
            'questao'    => $questao,
        ]);
    }

    public function update(Request $request, $id) : JsonResponse
    {
        $questao = Questao::find($id);

        if(!$questao) {
            return Response::json(['message' => 'Questão não encontrado'], 404);
        }

        DB::beginTransaction();
        try {
            $questao->fill($request->all());

            if($questao->isDirty()) {
                $questao->save();
            }

            if($request->filled('opcoes')) {
                foreach ($request->opcoes as $opcao) {
                    if(!isset($crm['id'])) {
                        $newOpcao = new OpcaoQuestao($opcao);
                        $newOpcao->questao_id = $questao->id;
                    } else {
                        $newOpcao = OpcaoQuestao::find($opcao['id']);
                        $newOpcao->fill($crm);
                    }

                    $validator = Validator::make($newOpcao->getAttributes(), $newOpcao->rules());

                    if($validator->fails()) {
                        DB::rollBack();
                        return Response::json(['message' => $validator->errors()->all()], 422);
                    }

                    $newOpcao->save();
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar questão '. $e);
            return Response::json('Erro ao atualizar questão', 500);
        }

        DB::commit();
        return Response::json(['message' => 'Questão atualizada com sucesso']);
    }

    public function destroy($id)
    {
        try {
            $questao = Questao::find($id);

            if(!$questao) {
                return Response::json(['message' => 'questão não encontrada'], 404);
            }

            $questao->forceDelete();
        } catch (\Exception $e) {
            Log::error('Erro ao deletar questão '. $e);

            return Response::json(['message' => 'Erro ao deletar questão'], 500);
        }

        return Response::json(['message' => 'Questão deletada com sucesso.'], 200);
    }

    public function destroyOpcao($id)
    {
        try {
            $opcao = OpcaoQuestao::find($id);

            if(!$opcao) {
                return Response::json(['message' => 'Opção não encontrada'], 404);
            }

            $opcao->forceDelete();
        } catch (\Exception $e) {
            Log::error('Erro ao deletar opção '. $e);

            return Response::json(['message' => 'Erro ao deletar opção'], 500);
        }

        return Response::json(['message' => 'Opção deletada com sucesso.'], 200);
    }

    public function vincularQuestaoQuestionario(Request $request) : JsonResponse
    {
        DB::beginTransaction();
        try {
            QuestaoQuestionario::where('questionario_id', $request->vinculos[0]['questionario_id'])->whereNotIn('questao_id', [1,2])->forceDelete();

            $arrId = [];
            foreach ($request->vinculos as $vinculo) {
                $vinculo = new QuestaoQuestionario($vinculo);

                $validator = Validator::make($vinculo->getAttributes(), $vinculo->rules());

                if($validator->fails()) {
                    DB::rollBack();
                    return Response::json(['message' => $validator->errors()->all()], 422);
                }

                $vinculo->save();

                $arrId[] = $vinculo->id;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao salvar vínculo'. $e);

            return Response::json(['message' => 'Erro ao salvar vínculo'], 500);
        }

        DB::commit();

        return Response::json([
            'message'    => 'Vínculos salvos com sucesso',
            'vinculos'   => QuestaoQuestionario::whereIn('id', $arrId)->get(),
        ]);
    }

    public function destroyVinculo($id)
    {
        try {
            $vinculo = QuestaoQuestionario::find($id);

            if(!$vinculo) {
                return Response::json(['message' => 'Vínculo não encontrada'], 404);
            }

            $vinculo->forceDelete();
        } catch (\Exception $e) {
            Log::error('Erro ao deletar vínculo '. $e);

            return Response::json(['message' => 'Erro ao deletar vínculo'], 500);
        }

        return Response::json(['message' => 'Vínculo deletado com sucesso.'], 200);
    }

    public function storeResposta(Request $request) : JsonResponse
    {
        DB::beginTransaction();
        try {
            if(!$request->filled('questionario_id')) {
                return Response::json(['message' => 'É obrigatório o id do questionario.'], 422);
            }

            $questionario = Questionario::find($request->questionario_id);

            if(!$questionario){
                return Response::json(['message' => 'Questionario não encontrado.'], 404);
            }

            foreach ($questionario->questoes as $questao) {
                $ids = $questao->respostas->pluck('id');

                QuestaoQuestionarioResposta::whereIn('id', $ids)->whereDate('created_at', Carbon::now())->forceDelete();
            }

            $arrId = [];
            foreach ($request->respostas as $resposta) {
                $resposta = new QuestaoQuestionarioResposta($resposta);

                $validator = Validator::make($resposta->getAttributes(), $resposta->rules());
                if($validator->fails()) {
                    return Response::json(['message' => $validator->errors()->all()], 422);
                }

                if(!QuestaoQuestionario::where('questionario_id', $questionario->id)->where('id', $resposta['questionario_questao_id'])->first()) {
                    return Response::json(['message' => 'Esta questao não existe neste questionário'], 422);
                }

                $resposta->save();

                $arrId[] = $resposta->id;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao salvar respostas'. $e);

            return Response::json(['message' => 'Erro ao salvar respostas'], 500);
        }

        DB::commit();

        return Response::json([
            'message'   => 'Respostas salvas com sucesso',
            'respostas' => QuestaoQuestionarioResposta::whereIn('id', $arrId)->get(),
        ]);
    }
}
