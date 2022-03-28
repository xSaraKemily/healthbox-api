<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Especializacao;
use Illuminate\Http\Request;

class EspecializacaoController extends Controller
{
   public function index(Request $request)
   {
       $espec =  Especializacao::select('id', 'nome');

       if($request->filled('nome')) {
           $espec = $espec->where('nome', 'LIKE', "%$request->nome%");
       }

       return $espec->paginate(100);
   }
}
