<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Controller;
use App\Http\Requests\LikeRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\CaracteristicaMedico;
use App\Models\CaracteristicaPaciente;
use App\Models\Like;
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

class LikeController extends Controller
{
    public function index(Request $request)
    {
        $likes = Like::select('*');

         if($request->filled('opiniao_id')) {
             $likes = $likes->where('opiniao_id', $request->opiniao_id);
         }

        return $likes->paginate(10);
    }

    public function store(Request $request) : JsonResponse
    {
        $like = Like::where('usuario_id', auth()->user()->id)->where('opiniao_id', $request->opiniao_id)->withTrashed()->first();

        DB::beginTransaction();

        if($like) {
            if ($like->deleted_at) {
                $like->restore();
            }

            $like->fill($request->all());
        } else {
            $like = new Like($request->all());
        }

        $like->usuario_id = auth()->user()->id;

        $validator = Validator::make($like->getAttributes(), $like->rules());

        if($validator->fails()) {
            return Response::json(['message' => $validator->errors()->all()], 422);
        }

        try {
            $like->save();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao salvar like '. $e);

            return Response::json(['message' => 'Erro ao salvar like.'], 500);
        }

        DB::commit();

        return Response::json([
            'message' => 'Like salvo com sucesso.',
            'ilike'' => $like
            ]);
    }

    public function destroy($id)
    {
        try {
            Like::find($id)->delete();
        } catch (\Exception $e) {
            Log::error('Erro ao deletar like '. $e);

            return Response::json(['message' => 'Erro ao deletar like.'], 500);
        }

        return Response::json(['message' => 'Like salvo com sucesso.'], 200);
    }
}
