<?php

namespace App\Http\Controllers\Api;

use App\Actions\ValidaEstadoAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\EspecializacaoRequest;
use App\Http\Requests\MedicoCrmEspecializacaoRequest;
use App\Http\Requests\MedicoCrmRequest;
use App\Models\Especializacao;
use App\Models\MedicoCrm;
use App\Models\MedicoCrmEspecializacao;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class EspecializacaoController extends Controller
{
   public function index(Request $request)
   {
       $espec =  Especializacao::select('id', 'nome');

       if($request->filled('nome')) {
           $espec = $espec->where('nome', 'LIKE', "%$request->nome%");
       }

       return $espec->paginate(100);
   }

    public function store(MedicoCrmEspecializacaoRequest $request) : JsonResponse
    {
        $especializacao = new MedicoCrmEspecializacao($request->all());

        if(MedicoCrmEspecializacao::where('medico_crm_id', $especializacao->medico_crm_id)->count() >= 2) {
            return Response::json(['message' => 'Cada CRM pode ter no máximo 2 especializações.'], 422);
        }

        DB::beginTransaction();

        try {
            $especializacao->save();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao salvar especializaação '. $e);

            return Response::json(['message' => 'Erro ao salvar especializaação.'], 500);
        }

        DB::commit();

        return Response::json([
            'message'        => 'Especializaação salva com sucesso',
            'especializacao' => $especializacao,
        ]);
    }

    public function destroy($id)
    {
        try {
            MedicoCrmEspecializacao::find($id)->delete();
        } catch (\Exception $e) {
            Log::error('Erro ao deletar CRM '. $e);

            return Response::json(['message' => 'Erro ao deletar especializaação'], 500);
        }

        return Response::json(['message' => 'Especializaação deletado com sucesso.']);
    }
}
