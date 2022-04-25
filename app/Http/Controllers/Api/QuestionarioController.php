<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcompanhamentoRequest;
use App\Http\Requests\QuestionarioRequest;
use App\Models\Acompanhamento;
use App\Models\Questionario;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class QuestionarioController extends Controller
{
    public function index(Request $request)
    {
        return Questionario::select('*')->paginate(100);
    }

    public function store(QuestionarioRequest $request): JsonResponse
    {
        $questionario = new Questionario($request->all());

        if(!self::validateQuantity($questionario->acompanhamento_id)) {
            return Response::json(['message' => 'Já existe questionário para este acompanhamento.'], 422);
        }

        DB::beginTransaction();

        try {
            $questionario->save();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao salvar questionário ' . $e);

            return Response::json(['message' => 'Erro ao salvar questionário.'], 500);
        }

        DB::commit();

        return Response::json([
            'message'      => 'Questionário salvo com sucesso',
            'questionario' => $questionario,
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $questionario = Questionario::find($id);

        if (!$questionario) {
            return Response::json(['message' => 'Questionário não encontrado.'], 404);
        }

        $questionario->fill($request->all());

        DB::beginTransaction();

        try {
            $validator = Validator::make($questionario->getAttributes(), $questionario->rules());

            if($validator->fails()) {
                return Response::json(['message' => $validator->errors()->all()], 422);
            }

            if(!self::validateQuantity($questionario->acompanhamento_id, $questionario->id)) {
                return Response::json(['message' => 'Já existe questionário para este acompanhamento.'], 422);
            }

            $questionario->save();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar questionário ' . $e);

            return Response::json(['message' => 'Erro ao atualizar questionário.'], 500);
        }

        DB::commit();

        return Response::json(['message' => 'Questionário atualizado com sucesso']);
    }

    //todo: testar
    public function destroy($id)
    {
        try {
            $questionario = Questionario::find($id);

            if (!$questionario) {
                return Response::json(['message' => 'Questionário não encontrado.'], 404);
            }

            if ($questionario->questoes) {
                foreach ($questionario->questoes as $questao) {
                    $questao->resposta->delete();
                    $questao->delete();
                }
            }

            $questionario->delete();
        } catch (Exception $e) {
            Log::error('Erro ao deletar CRM ' . $e);

            return Response::json(['message' => 'Erro ao deletar questionário'], 500);
        }

        return Response::json(['message' => 'Questionário deletado com sucesso.']);
    }

    /**
     * @param int $acompanhamentoId
     * @return bool
     *
     * Valida se pode criar questionario para acompanhamento, maximo 1
     */
    public static function validateQuantity(int $acompanhamentoId, $questionarioId = null): bool
    {
        $quest = Questionario::where('acompanhamento_id', $acompanhamentoId);

        if($questionarioId) {
            $quest = $quest->where('id', '<>', $questionarioId);
        }

        return $quest->first() ? false : true;
    }
}
