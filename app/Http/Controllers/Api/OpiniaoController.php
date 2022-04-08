<?php

namespace App\Http\Controllers\Api;

use App\Actions\ValidaEstadoAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\EspecializacaoRequest;
use App\Http\Requests\MedicoCrmEspecializacaoRequest;
use App\Http\Requests\MedicoCrmRequest;
use App\Http\Requests\OpiniaoRequest;
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

class OpiniaoController extends Controller
{
   public function index(Request $request)
   {
       $opinioes =  Opiniao::select("*", DB::raw("COUNT(likes.id) as total_like"))->leftJoin('likes', 'likes.opiniao_id', 'opinioes.id');

       if($request->filled('ativo')) {
           $opinioes = $opinioes->where('ativo', $request->ativo);
       }

       if($request->filled('paciente_id')) {
           $opinioes = $opinioes->where('paciente_id', $request->paciente_id);
       }

       if($request->filled('order_eficaz') && in_array($request->order_eficaz, ['desc', 'asc'])) {
           $opinioes = $opinioes->orderBy('paciente_id', $request->order_eficaz);

       } else if($request->filled('order_likes') && in_array($request->order_likes, ['desc', 'asc'])) {
           $opinioes = $opinioes->orderBy('total_like', $request->order_eficaz);
       }

       return $opinioes->groupBy('opinioes.id')->paginate(10);
   }

    public function store(OpiniaoRequest $request) : JsonResponse
    {
        $opiniao = new Opiniao($request->all());

        DB::beginTransaction();

        try {
            $opiniao->save();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao salvar opinião '. $e);

            return Response::json(['message' => 'Erro ao salvar opinião.'], 500);
        }

        DB::commit();

        return Response::json([
            'message'        => 'Opinião salva com sucesso',
            'opiniao' => $opiniao,
        ]);
    }

    public function update(Request $request, $id) : JsonResponse
    {
        $opiniao = Opiniao::find($id);

        DB::beginTransaction();

        try {
           if($request->filled('paciente_id')) {

           }

            $opiniao->fill($request->all());

            $validator = Validator::make($opiniao->getAttributes(), $opiniao->rules());

            if($validator->fails()) {
                return Response::json(['message' => $validator->errors()->all()], 422);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar opinião '. $e);

            return Response::json(['message' => 'Erro ao atualizar opinião.'], 500);
        }

        DB::commit();

        return Response::json([
            'message' => 'Opinião atualizada com sucesso',
        ]);
    }

    public function destroy($id)
    {
        try {
            $opiniao = Opiniao::find($id);

            $tratamento = Tratamento::where('opiniao_id', $opiniao->id)->first();

            RemedioTratamento::where('tratamento_id', $tratamento->id)->delete();

            $tratamento->delete();

            $opiniao->delete();
        } catch (\Exception $e) {
            Log::error('Erro ao deletar opinião '. $e);

            return Response::json(['message' => 'Erro ao deletar opinião'], 500);
        }

        return Response::json(['message' => 'Opinião deletada com sucesso.']);
    }
}
