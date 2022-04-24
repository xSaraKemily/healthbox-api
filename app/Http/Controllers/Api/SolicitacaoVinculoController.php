<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SolicitacaoVinculoRequest;
use App\Models\SolicitacaoVinculo;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class SolicitacaoVinculoController extends Controller
{
    public function index(Request $request)
    {
        $columns = self::getColumnsWhere();

        $solicitacoes = SolicitacaoVinculo::where($columns->colunaUser, auth()->user()->id)
            ->where('vinculado', $request->vinculado)
            ->with(['solicitante', 'solicitado']);

        if (!$request->vinculado) {
            $solicitacoes->where('solicitante_id', '<>', auth()->user()->id);
        }

        return $solicitacoes->paginate(10);
    }

    /**
     * @param SolicitacaoVinculoRequest $request
     * @return JsonResponse
     */
    public function store(SolicitacaoVinculoRequest $request): JsonResponse
    {
        $vinculoDeleted = SolicitacaoVinculo::where('medico_id', $request->medico_id)->where('paciente_id', $request->paciente_id)->withTrashed()->first();

        if ($vinculoDeleted) {
            $vinculo = $vinculoDeleted;
            $vinculo->restore();
        } else {
            $vinculo = new SolicitacaoVinculo($request->all());
        }

        DB::beginTransaction();

        try {
            $vinculo->solicitante_id = auth()->user()->id;
            $vinculo->save();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao salvar vínculo ' . $e);

            return Response::json(['message' => 'Erro ao salvar vínculo.'], 500);
        }

        DB::commit();

        return Response::json([
            'message' => 'Vínculo salvo com sucesso',
            'vinculo' => $vinculo,
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $vinculo = SolicitacaoVinculo::find($id);

        if (!$vinculo) {
            return Response::json(['message' => 'Vínculo não encontrado.'], 404);
        }

        if ($request->filled('vinculado')) {
            $vinculo->vinculado = $request->vinculado;
        }

        if ($vinculo->isDirty()) {
            DB::beginTransaction();

            try {
                $vinculo->save();
            } catch (Exception $e) {
                DB::rollBack();
                Log::error('Erro ao atualizar vínculo ' . $e);

                return Response::json(['message' => 'Erro ao atualizar vínculo.'], 500);
            }

            DB::commit();
        }

        return Response::json([
            'message' => 'Vínculo atualizado com sucesso',
        ]);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $vinculo = SolicitacaoVinculo::find($id);

            if (!$vinculo) {
                return Response::json(['message' => 'Vínculo não encontrado.'], 404);
            }

            $vinculo->delete();
        } catch (Exception $e) {
            Log::error('Erro ao deletar vínculo ' . $e);

            return Response::json(['message' => 'Erro ao deletar vínculo'], 500);
        }

        return Response::json(['message' => 'Vínculo deletado com sucesso.']);
    }

    /**
     * @param Request $request
     * @return mixed
     * Retorna usuarios que podem se vincular com o usuario logado
     */
    public function userParaVincular(Request $request)
    {
        $columns = self::getColumnsWhere();

        $vinculosUser = SolicitacaoVinculo::where($columns->colunaUser, auth()->user()->id)->select($columns->colunaOposta)->get()->toArray();

        $users = User::select('id', 'name', 'foto_path')->where('tipo', $columns->tipoOposto)->whereNotIn('id', $vinculosUser);

        if ($request->filled('nome')) {
            $users->where('name', 'ilike', "%$request->nome%");
        }

        return $users->paginate(10);
    }

    public static function getColumnsWhere()
    {
        switch (auth()->user()->tipo) {
            case 'M':
                {
                    $colunaUser = 'medico_id';
                    $colunaOposta = 'paciente_id';
                    $tipoOposto = 'P';
                }
                break;
            case 'P':
                {
                    $colunaUser = 'paciente_id';
                    $colunaOposta = 'medico_id';
                    $tipoOposto = 'M';
                }
                break;
        }

        return (object)['colunaUser' => $colunaUser, 'colunaOposta' => $colunaOposta, 'tipoOposto' => $tipoOposto];
    }
}
