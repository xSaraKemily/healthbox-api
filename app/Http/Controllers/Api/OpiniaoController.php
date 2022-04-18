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
       $opinioes =  Opiniao::select("opinioes.*", DB::raw("COUNT(likes.id) as total_like"), DB::raw("COUNT(dislike.id) as total_dislike"))
           ->leftJoin('likes', function ($query) {
               $query->on('likes.opiniao_id', 'opinioes.id')
                   ->where('likes.is_like', true);
           })
           ->leftJoin('likes as dislike', function ($query) {
               $query->on('dislike.opiniao_id', 'opinioes.id')
                   ->where('dislike.is_like', false);
           })
       ->with(['tratamento' => function($query){
           $query->with(['remedios' => function($sub) {
               $sub->with('remedio');
           }]);
       }])
       ->with(['likes' => function($query) {
           $query->select('usuario_id', 'opiniao_id', 'is_like', 'id');
       }])
       ->with('paciente');

       if($request->filled('remedios') || $request->filled('titulo')) {
           $opinioes = $opinioes->join('tratamentos as tt', function($query) use($request){
               $query->on('tt.opiniao_id', 'opinioes.id');

               if($request->remedios) {
                   $remedios = $request->remedios;
                   if(!is_array($remedios)) {
                       $remedios = explode(',', $request->remedios);
                   }

                   $query->join('remedios_tratamentos as ret', 'ret.tratamento_id', 'tt.id')
                        ->whereIn('remedio_id', $remedios);
               }

               if($request->titulo) {
                   $query->where('tt.titulo', 'like', $request->titulo);
               }
            });
       }

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
           if($request->order_dislikes == 'desc') {
               $opinioes = $opinioes->orderBy('total_dislike', 'desc');
           }

           if($request->order_dislikes == 'asc') {
               $opinioes = $opinioes->orderBy('total_dislike', 'asc');
           }
       }

       if($request->filled('order_data') && in_array($request->order_data, ['desc', 'asc'])) {
           if($request->order_data == 'desc') {
               $opinioes = $opinioes->orderBy('created_at', 'desc');
           }

           if($request->order_data == 'asc') {
               $opinioes = $opinioes->orderBy('created_at', 'asc');
           }
       }

       //todo: filtro de remedio
        return $opinioes->groupBy(
           'opinioes.id'
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
