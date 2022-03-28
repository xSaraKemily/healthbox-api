<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Controller;
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

class UserController extends Controller
{
    /**
     * Store a new user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) : JsonResponse
    {
        $user = new User($request->all());

        $validator = Validator::make($user->getAttributes(), $user->rules());

        if($validator->fails()) {
            return Response::json(['message' => $validator->errors()->all()], 422);
        }

        $senha =  Hash::make($request->password);

        $request->merge(['password' => $senha]);

        DB::beginTransaction();
        try {
            $user = new User($request->all());
            $user->save();

            //se for medico
            if($user->tipo == 'M') {
                if($request->filled('caracteristicas')) {
                    $caracteristicas = new CaracteristicaMedico($request->caracteristicas);
                    $caracteristicas->medico_id = $user->id;

                    $validator = Validator::make($caracteristicas->getAttributes(), $caracteristicas->rules());

                    if($validator->fails()) {
                        DB::rollBack();
                        return Response::json(['message' => $validator->errors()->all()], 422);
                    }

                    $caracteristicas->save();
                }

                if($request->filled('crms')) {
                    foreach ($request->crms as $crm) {
                        $newCrm = new MedicoCrm($crm);
                        $newCrm->medico_id = $user->id;

                        $validator = Validator::make($newCrm->getAttributes(), $newCrm->rules());

                        if($validator->fails()) {
                            DB::rollBack();
                            return Response::json(['message' => $validator->errors()->all()], 422);
                        }

                        $newCrm->save();

                        if(isset($crm['especializacoes'])) {
                            if(count($crm['especializacoes']) > 2) {
                                DB::rollBack();
                                return Response::json(['message' => 'Um CRM não pode ter mais de 2 especializações'], 422);
                            }

                            foreach ($crm['especializacoes'] as $especializacao) {
                                $especializacao = new MedicoCrmEspecializacao($especializacao);
                                $especializacao->medico_crm_id = $newCrm->id;

                                $validator = Validator::make($especializacao->getAttributes(), $especializacao->rules());

                                if($validator->fails()) {
                                    DB::rollBack();
                                    return Response::json(['message' => $validator->errors()->all()], 422);
                                }

                                $especializacao->save();
                            }
                        }
                    }
                }
            } else {
                if($request->filled('caracteristicas')) {
                    //se for paciente
                    $caracteristicas = new CaracteristicaPaciente($request->caracteristicas);
                    $caracteristicas->paciente_id = $user->id;

                    $validator = Validator::make($caracteristicas->getAttributes(), $caracteristicas->rules());

                    if($validator->fails()) {
                        DB::rollBack();
                        return Response::json(['message' => $validator->errors()->all()], 422);
                    }

                    $caracteristicas->save();
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao salvar usuario '. $e);

            return Response::json(['message' => 'Erro ao salvar usuário'], 500);
        }

        DB::commit();

        return Response::json([
            'message' => 'Usuário salvo com sucesso',
            'user'    => $user,
        ]);
    }

    public function update(StoreUserRequest $request, $id) : JsonResponse
    {
        $user = User::find($id);

        if(!$user) {
            return Response::json(['message' => 'Usuário não encontrado'], 404);
        }

        if(!Hash::check($request->password, $user->password)) {
            $senha =  Hash::make($request->password);
            $request->merge(['password' => $senha]);
        }

        //sobrescreve tipo de usuario do request pois nao pode atualizar
        $request->merge(['tipo'=> $user->tipo]);

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
                        return Response::json(['message' => $validator->errors()->all()], 422);
                    }
                }

                $caracteristicas->save();
            }
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar usuario '. $e);

            return Response::json('Erro ao atualizar usuário', 500);
        }

        return Response::json('Usuário atualizaco com sucesso');
    }

    public function validateData(Request $request) : JsonResponse
    {
        $validacao = [];

        if($request->filled('email')){
            $user = User::where('email', $request->email)->first();

            $bool = true;
            if($user) {
                $bool = false;
            }

            $validacao['email']['validate'] = $bool;
        }

        if($request->filled('crm')){
            $medicoCrm = MedicoCrm::where('crm', $request->crm)->first();

            $bool = true;
            if($medicoCrm) {
                $bool = false;
            }

            $validacao['crm']['validate'] = $bool;
        }

        if($request->filled('cpf')){
            $pacienteCpf = CaracteristicaPaciente::where('cpf', $request->cpf)->first();

            $bool = true;
            if($pacienteCpf) {
                $bool = false;
            }

            $validacao['cpf']['validate'] = $bool;
        }

        return Response::json($validacao, 200);
    }
}
