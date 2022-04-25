<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcompanhamentoRequest;
use App\Models\Acompanhamento;
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
        return Acompanhamento::select('*')->paginate(100);
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
}
