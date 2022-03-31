<?php

namespace App\Http\Controllers\Api;

use App\Models\MedicoCrm;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Actions\ValidaEstadoAction;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\MedicoCrmRequest;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class MedicoCrmController extends Controller
{
    public function store(MedicoCrmRequest $request) : JsonResponse
    {
        $crm = new MedicoCrm($request->all());

        if(!ValidaEstadoAction::run($request->estado_sigla)) {
            return Response::json(['message' => 'Estado inválido.'], 422);
        }

        DB::beginTransaction();

        try {
            $crm->save();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao salvar CRM '. $e);

            return Response::json(['message' => 'Erro ao salvar CRM.'], 500);
        }

        DB::commit();

        return Response::json([
            'message' => 'CRM salvo com sucesso',
            'crm'     => $crm,
        ]);
    }

    public function update(Request $request, $id) : JsonResponse
    {
        $crm = MedicoCrm::find($id);

        if(!$crm) {
            return Response::json(['message' => 'CRM não encontrado.'], 404);
        }

        try {
            $crm->fill($request->all());

            if(!ValidaEstadoAction::run($crm->estado_sigla)) {
                return Response::json(['message' => 'Estado inválido.'], 422);
            }

            $validator = Validator::make($crm->getAttributes(), $crm->rules());

            if($validator->fails()) {
                return Response::json(['message' => $validator->errors()->all()], 422);
            }

            $crm->save();
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar CRM '. $e);

            return Response::json(['message' => 'Erro ao atualizar CRM.'], 500);
        }

        return Response::json([
            'message' => 'CRM atualizado com sucesso',
            'crm'     => $crm,
        ]);
    }

    public function destroy($id)
    {
        try {
            MedicoCrm::find($id)->delete();
        } catch (\Exception $e) {
            Log::error('Erro ao deletar CRM '. $e);

            return Response::json(['message' => 'Erro ao deletar CRM'], 500);
        }

        return Response::json(['message' => 'CRM deletado com sucesso.']);
    }
}
