<?php

namespace App\Http\Controllers\Api;

use App\Actions\ValidaEstadoAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\EspecializacaoRequest;
use App\Http\Requests\MedicoCrmEspecializacaoRequest;
use App\Http\Requests\MedicoCrmRequest;
use App\Http\Requests\OpiniaoRequest;
use App\Http\Requests\TratamentoRequest;
use App\Models\Especializacao;
use App\Models\Like;
use App\Models\MedicoCrm;
use App\Models\MedicoCrmEspecializacao;
use App\Models\Opiniao;
use App\Models\RemedioTratamento;
use App\Models\Tratamento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class TratamentoController extends Controller
{
    public function store(TratamentoRequest $request) : JsonResponse
    {
        $tratamento = new Tratamento($request->all());

        DB::beginTransaction();

        try {
            $tratamento->save();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao salvar tratamento '. $e);

            return Response::json(['message' => 'Erro ao salvar tratamento.'], 500);
        }

        DB::commit();

        return Response::json([
            'message'    => 'Tratamento salvo com sucesso',
            'tratamento' => $tratamento,
        ]);
    }

    public function update(Request $request, $id) : JsonResponse
    {
        $tratamento = Tratamento::find($id);

        DB::beginTransaction();

        try {
            $tratamento->fill($request->all());

            $validator = Validator::make($tratamento->getAttributes(), $tratamento->rules());

            if($validator->fails()) {
                return Response::json(['message' => $validator->errors()->all()], 422);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar tratamento '. $e);

            return Response::json(['message' => 'Erro ao atualizar tratamento.'], 500);
        }

        DB::commit();

        return Response::json([
            'message' => 'Tratamento atualizado com sucesso',
        ]);
    }

    public function destroy($id)
    {
        try {
            $tratamento = Tratamento::find($id);

            RemedioTratamento::where('tratamento_id', $tratamento->id)->delete();

            $tratamento->delete();
        } catch (\Exception $e) {
            Log::error('Erro ao deletar tratamento '. $e);

            return Response::json(['message' => 'Erro ao deletar tratamento'], 500);
        }

        return Response::json(['message' => 'Tratamento deletado com sucesso.']);
    }
}
