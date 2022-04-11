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

        $donoTratamento = $tratamento->opiniao ? $tratamento->opiniao->paciente_id : $tratamento->acompanhamento->medico_id;

        if($donoTratamento != auth()->user()->id) {
            return Response::json(['message' => 'Erro ao salvar tratamento.'], 500);
        }

        DB::beginTransaction();

        try {
            $tratamento->save();

            if($request->filled('remedios')) {
                foreach ($request->remedios as $remedio) {
                    $newRemedio = new RemedioTratamento($remedio);
                    $newRemedio->tratamento_id = $tratamento->id;

                    $validator = Validator::make($newRemedio->getAttributes(), $newRemedio->rules());

                    if($validator->fails()) {
                        DB::rollBack();
                        return Response::json(['message' => $validator->errors()->all()], 422);
                    }

                    $newRemedio->save();
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao salvar tratamento '. $e);

            return Response::json(['message' => 'Erro ao salvar tratamento.'], 500);
        }

        DB::commit();

        $tratamento->makeHidden('opiniao');
        $tratamento->remedios;

        return Response::json([
            'message'    => 'Tratamento salvo com sucesso',
            'tratamento' => $tratamento,
        ]);
    }

    public function update(Request $request, $id) : JsonResponse
    {
        $tratamento = Tratamento::find($id);

        if(!$tratamento) {
            return Response::json(['message' => 'Tratamento não encontrado'], 404);
        }

        DB::beginTransaction();

        try {
            $tratamento->fill($request->all());

            $validator = Validator::make($tratamento->getAttributes(), $tratamento->rules());

            if($validator->fails()) {
                return Response::json(['message' => $validator->errors()->all()], 422);
            }

            $tratamento->save();

            if($request->filled('remedios')) {
                foreach ($request->remedios as $remedio) {
                    if(isset($remedio['id'])) {
                        $newRemedio = RemedioTratamento::find($remedio['id']);

                        if(!$newRemedio) {
                            return Response::json(['message' => 'Vínculo com medicamento não encontrado.'], 404);
                        }

                        $newRemedio->fill($remedio);
                    } else {
                        $newRemedio = new RemedioTratamento($remedio);
                        $newRemedio->tratamento_id = $tratamento->id;
                    }

                    $validator = Validator::make($newRemedio->getAttributes(), $newRemedio->rules());

                    if($validator->fails()) {
                        DB::rollBack();
                        return Response::json(['message' => $validator->errors()->all()], 422);
                    }

                    $newRemedio->save();
                }
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

            if(!$tratamento) {
                return Response::json(['message' => 'Tratamento não encontrado.'], 404);
            }

            RemedioTratamento::where('tratamento_id', $tratamento->id)->delete();

            $tratamento->delete();
        } catch (\Exception $e) {
            Log::error('Erro ao deletar tratamento '. $e);

            return Response::json(['message' => 'Erro ao deletar tratamento'], 500);
        }

        return Response::json(['message' => 'Tratamento deletado com sucesso.']);
    }
}
