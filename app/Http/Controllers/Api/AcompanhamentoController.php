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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class AcompanhamentoController extends Controller
{
   public function index(Request $request)
   {
       return Acompanhamento::select('*')->paginate(100);
   }

    public function store(AcompanhamentoRequest $request) : JsonResponse
    {
        $acompanhamento = new Acompanhamento($request->all());

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
