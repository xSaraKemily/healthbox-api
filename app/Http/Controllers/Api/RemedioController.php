<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\CaracteristicaMedico;
use App\Models\CaracteristicaPaciente;
use App\Models\MedicoCrm;
use App\Models\MedicoCrmEspecializacao;
use App\Models\Remedio;
use App\Models\User;
use App\Utils\Functions;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class RemedioController extends Controller
{
    public function index(Request $request)
    {
        if(!$request->filled('nome')) {
            return Response::json(['message' => 'É obrigatório informar o nome do remédio'], 422);
        }

        //se nao existir no banco, busca na api
        $remedios = Remedio::where('nome', 'ilike', '%'.$request->nome.'%')->paginate(10);

       if($remedios->isEmpty()) {
           $response = Http::get('https://bula.vercel.app/pesquisar', [
               'nome'   => $request->nome,
               'pagina' => $request->page
           ]);

           $remedios = json_decode($response->body());

           if(!isset($remedios->content)) {
               return $remedios;
           }

           foreach ($remedios->content as $remedio) {
               if(Remedio::where('api_id', $remedio->idProduto)->first()) {
                   continue;
               }

               $new = new Remedio;
               $new->api_id         = $remedio->idProduto;
               $new->fabricante     = $remedio->razaoSocial;
               $new->hash_pdf_bula  = $remedio->idBulaPacienteProtegido;
               $new->nome           = $remedio->nomeProduto;
               $new->save();
           }
       } else {
           return $remedios;
       }

        return Remedio::where('nome', 'ilike', '%'.$request->nome.'%')->paginate(10);
    }

    /**
     * Traz todos os medicamentos usados nas opinioes
     * Não tem paginacao
     **/
    public function getUsed($tipo = 'opinioes')
    {
        $columns = Functions::getColumnsWhere();

        $remedios = Remedio::join('remedios_tratamentos as rt', 'rt.remedio_id', 'remedios.id')
            ->join('tratamentos', 'tratamentos.id', 'rt.tratamento_id');

        if ($tipo == 'acompanhamentos') {
            $remedios = $remedios->join('acompanhamentos', 'acompanhamentos.id', 'tratamentos.acompanhamento_id')
                ->where('acompanhamentos.'.$columns->colunaUser, auth()->user()->id);
        } else {
            $remedios = $remedios->join('opinioes', 'opinioes.id', 'tratamentos.opiniao_id');
        }

        $remedios = $remedios->groupBy('remedios.id', 'remedios.nome', 'remedios.fabricante')
            ->select('remedios.*')
            ->get();

        return Response::json($remedios);
    }
}
