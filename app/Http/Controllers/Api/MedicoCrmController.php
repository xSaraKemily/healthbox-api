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

    public function update(Request $request, $id) : JsonResponse
    {
        $user = User::find($id);

        if(!$user) {
            return Response::json(['message' => 'Usuário não encontrado'], 404);
        }

        if(!Hash::check($request->password, $user->password)) {
            $senha =  Hash::make($request->password);
            $request->merge(['password' => $senha]);
        } else {
            $request->merge(['password' => $user->password]);
        }

        //sobrescreve tipo de usuario do request pois nao pode atualizar
        $request->merge(['tipo'=> $user->tipo]);

        DB::beginTransaction();
        try {
            $user->fill($request->all());

            if($user->isDirty()) {
                $user->save();
            }

            //se for medico
            if($user->tipo == 'M') {
                if($request->filled('caracteristicas')) {
                    $caracteristicas = CaracteristicaMedico::where('medico_id', $user->id)->first();

                    if($caracteristicas) {
                        $caracteristicas->fill($request->all());

                        $validator = Validator::make($caracteristicas->getAttributes(), $caracteristicas->rules());

                        if($validator->fails()) {
                            return Response::json(['message' => $validator->errors()->all()] , 422);
                        }

                        if($caracteristicas->isDirty()) {
                            $caracteristicas->save();
                        }
                    }
                }

                if($request->filled('crms')) {
                    foreach ($request->crms as $crm) {
                        if(!isset($crm['id'])) {
                            $newCrm = new MedicoCrm($crm);
                            $newCrm->medico_id = $user->id;
                        } else {
                            $newCrm = MedicoCrm::find($crm['id']);
                            $newCrm->fill($crm);
                        }

                        $validator = Validator::make($newCrm->getAttributes(), $newCrm->rules());

                        if($validator->fails()) {
                            DB::rollBack();
                            return Response::json(['message' => $validator->errors()->all()], 422);
                        }

                        $newCrm->save();

                        if(isset($crm['especializacoes'])) {
                            foreach ($crm['especializacoes'] as $especializacao) {
                                if(!isset($especializacao['id'])) {
                                    if(MedicoCrmEspecializacao::where('medico_crm_id', $newCrm->id)->count() >= 2) {
                                        DB::rollBack();
                                        return Response::json(['message' => 'Um CRM não pode ter mais que 2 especializações'], 422);
                                    }

                                    $newEspecializacao = new MedicoCrmEspecializacao($especializacao);
                                    $newEspecializacao->medico_crm_id = $newCrm->id;
                                } else {
                                    $newEspecializacao = MedicoCrmEspecializacao::find($especializacao['id']);
                                    $newEspecializacao->fill($especializacao);
                                }

                                $validator = Validator::make($newEspecializacao->getAttributes(), $newEspecializacao->rules());

                                if($validator->fails()) {
                                    DB::rollBack();
                                    return Response::json(['message' => $validator->errors()->all()], 422);
                                }

                                $newEspecializacao->save();
                            }
                        }
                    }
                }
            } else {
                //se for paciente
                $caracteristicas = CaracteristicaPaciente::where('paciente_id', $user->id)->first();

                if($caracteristicas) {
                    $validator = Validator::make($caracteristicas->getAttributes(), $caracteristicas->rules());

                    if($validator->fails()) {
                        DB::rollBack();
                        return Response::json(['message' => $validator->errors()->all()], 422);
                    }
                }

                $caracteristicas->save();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar usuario '. $e);
            return Response::json('Erro ao atualizar usuário', 500);
        }

        DB::commit();
        return Response::json(['message' => 'Usuário atualizaco com sucesso']);
    }

    public function destroy($id)
    {
        try {
            User::find($id)->delete();
        } catch (\Exception $e) {
            Log::error('Erro ao deletar usuario '. $e);

            return Response::json(['message' => 'Erro ao deletar usuário'], 500);
        }

        auth()->logout();

        return Response::json(['message' => 'Usuário deletado com sucesso.'], 200);
    }
}
