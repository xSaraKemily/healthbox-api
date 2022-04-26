<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuestaoRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\Acompanhamento;
use App\Models\CaracteristicaMedico;
use App\Models\CaracteristicaPaciente;
use App\Models\MedicoCrm;
use App\Models\MedicoCrmEspecializacao;
use App\Models\OpcaoQuestao;
use App\Models\Questao;
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
        return Questao::where('usuario_id', auth()->user()->id)->get();
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
}
