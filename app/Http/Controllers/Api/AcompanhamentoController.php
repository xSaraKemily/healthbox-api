<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Acompanhamento;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use App\Http\Requests\AcompanhamentoRequest;

class AcompanhamentoController extends Controller
{
   public function index(Request $request)
   {
       return Acompanhamento::select('*')->paginate(100);
   }

    public function store(AcompanhamentoRequest $request) : JsonResponse
    {
        $acompanhamento = new Acompanhamento($request->all());
        $acompanhamento->medico_id = auth()->user()->id;

        DB::beginTransaction();

        try {
            $acompanhamento->save();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao salvar acompanhamento '. $e);

            return Response::json(['message' => 'Erro ao salvar acompanhamento.'], 500);
        }

        DB::commit();

        return Response::json([
            'message'        => 'Acompanhamento salvo com sucesso',
            'acompanhamento' => $acompanhamento,
        ]);
    }

    public function destroy($id)
    {
        try {
            $acompanhamento = Acompanhamento::find($id);

            if(!$acompanhamento) {
                return Response::json(['message' => 'Acompanhamento não encontrado.'], 404);
            }

            if($acompanhamento->tratamento) {
                if($acompanhamento->tratamento->remedios) {
                    $acompanhamento->tratamento->remedios->delete();
                }

                $acompanhamento->tratamento->delete();

                foreach ($acompanhamento->questionario->questoes as $questao) {
                    $questao->resposta->delete();
                }
            }

        } catch (\Exception $e) {
            Log::error('Erro ao deletar CRM '. $e);

            return Response::json(['message' => 'Erro ao deletar especializaação'], 500);
        }

        return Response::json(['message' => 'Especializaação deletado com sucesso.']);
    }
}
