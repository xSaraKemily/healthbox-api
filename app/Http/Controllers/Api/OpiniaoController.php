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
use App\Models\User;
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
       $opinioes =  Opiniao::select(
          [
              "opinioes.*",
              DB::raw("COUNT(likes.id) as total_like"),
              DB::raw("COUNT(dislike.id) as total_dislike"),
//              DB::raw("(CASE WHEN COUNT(likeUsu.id) > 0 THEN true ELSE false END) as usuario_like"),
              DB::raw("(CASE WHEN COUNT(dislikeUsu.id) > 0 THEN true ELSE false END) as usuario_dislike")
          ]
       )
           ->leftJoin('likes', function ($query) {
               $query->on('likes.opiniao_id', 'opinioes.id')
                   ->where('likes.is_like', true);
           })
           ->leftJoin('likes as dislike', function ($query) {
               $query->on('dislike.opiniao_id', 'opinioes.id')
                   ->where('dislike.is_like', false);
           })
           ->leftJoin('likes as likeUsu', function ($query) {
               $query->on('likeUsu.opiniao_id', 'opinioes.id')
                   ->where(function ($subQuery) {
                      $subQuery->where('likeUsu.is_like', true)
                          ->where('likeUsu.usuario_id', auth()->user()->id);
                   });
           })
           ->leftJoin('likes as dislikeUsu', function ($query) {
               $query->on('dislikeUsu.opiniao_id', 'opinioes.id')
                   ->where(function ($subQuery) {
                       $subQuery->where('dislikeUsu.is_like', true)
                           ->where('dislikeUsu.usuario_id', auth()->user()->id);
                   });
           })
       ->with(['tratamento' => function($query) {
           $query->with('remedios');
       }]);

       if($request->filled('ativo')) {
           $opinioes = $opinioes->where('ativo', $request->ativo);
       }

       if($request->filled('paciente_id')) {
           $opinioes = $opinioes->where('paciente_id', $request->paciente_id);
       }

       if($request->filled('eficaz')) {
           $opinioes = $opinioes->where('eficaz', $request->eficaz);
       }

       if($request->filled('order_likes') && in_array($request->order_likes, ['desc', 'asc'])) {
           //nao funcionou colocando direto o request

           if($request->order_likes == 'desc') {
               $opinioes = $opinioes->orderBy('total_like', 'desc');
           }

           if($request->order_likes == 'asc') {
               $opinioes = $opinioes->orderBy('total_like', 'asc');
           }
       }

       if($request->filled('order_dislikes') && in_array($request->order_dislikes, ['desc', 'asc'])) {
           //nao funcionou colocando direto o request

           if($request->order_dislikes == 'desc') {
               $opinioes = $opinioes->orderBy('total_dislike', 'desc');
           }

           if($request->order_dislikes == 'asc') {
               $opinioes = $opinioes->orderBy('total_dislike', 'asc');
           }
       }

       //todo: filtro de remedio

       return $opinioes->groupBy(
           'opinioes.id',
           'opinioes.descricao',
           'opinioes.paciente_id',
           'opinioes.eficaz',
           'opinioes.ativo',
           'opinioes.created_at',
           'opinioes.updated_at',
           'opinioes.deleted_at'
       )->paginate(10);
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

        if(!$opiniao) {
            return Response::json(['message' => 'Opinião não encontrada.'], 404);
        }

        if($opiniao->paciente->tipo == 'M') {
            return Response::json(['message' => 'Opiniões só podem ser atualizadas por pacientes.'], 500);
        }

        DB::beginTransaction();

        try {
            $opiniao->fill($request->all());

            $validator = Validator::make($opiniao->getAttributes(), $opiniao->rules());

            if($validator->fails()) {
                return Response::json(['message' => $validator->errors()->all()], 422);
            }

            $opiniao->save();
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

            if($tratamento) {
                RemedioTratamento::where('tratamento_id', $tratamento->id)->delete();

                $tratamento->delete();
            }

            $opiniao->delete();
        } catch (\Exception $e) {
            Log::error('Erro ao deletar opinião '. $e);

            return Response::json(['message' => 'Erro ao deletar opinião'], 500);
        }

        return Response::json(['message' => 'Opinião deletada com sucesso.']);
    }
}
