<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Controller;
use App\Http\Requests\MedicoCrmRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\CaracteristicaMedico;
use App\Models\CaracteristicaPaciente;
use App\Models\MedicoCrm;
use App\Models\MedicoCrmEspecializacao;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class MedicoCrmController extends Controller
{
    public function store(MedicoCrmRequest $request) : JsonResponse
    {
        $crm = new MedicoCrm($request->all());

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
                'user'    => $crm,
        ]);
    }
}
