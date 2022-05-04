<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\AuthController;
use \App\Http\Controllers\Api\UserController;
use \App\Http\Controllers\Api\LikeController;
use \App\Http\Controllers\Api\MedicoCrmController;
use \App\Http\Controllers\Api\EspecializacaoController;
use \App\Http\Controllers\Api\RemedioController;
use \App\Http\Controllers\Api\OpiniaoController;
use \App\Http\Controllers\Api\TratamentoController;
use \App\Http\Controllers\Api\GraficoController;
use \App\Http\Controllers\Api\SolicitacaoVinculoController;
use \App\Http\Controllers\Api\AcompanhamentoController;
use \App\Http\Controllers\Api\QuestionarioController;
use \App\Http\Controllers\Api\QuestaoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
    Route::post('register', [AuthController::class, 'register']);
});

Route::group(['middleware' => 'api'], function () {
    Route::get('especializacoes', [EspecializacaoController::class, 'index']);
    Route::get('usuarios/validate', [UserController::class, 'validateData']);
});

Route::group(['middleware' => 'auth:api',], function () {
    Route::put('usuarios/{id}', [UserController::class, 'update']);
    Route::delete('usuarios/{id}', [UserController::class, 'destroy']);

    Route::get('likes', [LikeController::class, 'index']);
    Route::post('likes', [LikeController::class, 'store']);
    Route::delete('likes/{id}',  [LikeController::class, 'destroy']);

    Route::post('crms', [MedicoCrmController::class, 'store']);
    Route::put('crms/{id}', [MedicoCrmController::class, 'update']);
    Route::delete('crms/{id}', [MedicoCrmController::class, 'destroy']);

    Route::post('especializacoes', [EspecializacaoController::class, 'store']);
    Route::delete('especializacoes/{id}', [EspecializacaoController::class, 'destroy']);

    Route::get('remedios', [RemedioController::class, 'index']);
    Route::get('remedios/usados', [RemedioController::class, 'getUsed']);

    Route::get('opinioes', [OpiniaoController::class, 'index']);
    Route::post('opinioes', [OpiniaoController::class, 'store']);
    Route::put('opinioes/{id}', [OpiniaoController::class, 'update']);
    Route::delete('opinioes/{id}', [OpiniaoController::class, 'destroy']);

    Route::post('tratamentos', [TratamentoController::class, 'store']);
    Route::put('tratamentos/{id}', [TratamentoController::class, 'update']);
    Route::delete('tratamentos/{id}', [TratamentoController::class, 'destroy']);

    Route::get('graficos/paciente-remedio', [GraficoController::class, 'pacienteRemedio']);
    Route::get('graficos/remedio-eficacia', [GraficoController::class, 'remedioEficacia']);
    Route::get('graficos/remedio-melhora', [GraficoController::class, 'remedioMelhora']);

    Route::get('solicitacoes-vinculos', [SolicitacaoVinculoController::class, 'index']);
    Route::post('solicitacoes-vinculos', [SolicitacaoVinculoController::class, 'store']);
    Route::put('solicitacoes-vinculos/{id}', [SolicitacaoVinculoController::class, 'update']);
    Route::delete('solicitacoes-vinculos/{id}', [SolicitacaoVinculoController::class, 'destroy']);
    Route::get('solicitacoes-vinculos/usuarios-disponiveis', [SolicitacaoVinculoController::class, 'userParaVincular']);

    Route::post('acompanhamentos', [AcompanhamentoController::class, 'store']);
    Route::get('acompanhamentos', [AcompanhamentoController::class, 'index']);
    Route::put('acompanhamentos/{id}', [AcompanhamentoController::class, 'update']);
    Route::delete('acompanhamentos/{id}', [AcompanhamentoController::class, 'destroy']);
    Route::get('acompanhamentos/{id}', [AcompanhamentoController::class, 'show']);
    Route::get('acompanhamentos/vinculos/usuarios', [AcompanhamentoController::class, 'usuarioVinculo']);
    Route::get('acompanhamentos/questionarios/responder', [AcompanhamentoController::class, 'questionariosResponder']);


    Route::post('questionarios', [QuestionarioController::class, 'store']);
    Route::put('questionarios/{id}', [QuestionarioController::class, 'update']);

    Route::get('questoes', [QuestaoController::class, 'index']);
    Route::get('questoes/{id}', [QuestaoController::class, 'show']);
    Route::post('questoes', [QuestaoController::class, 'store']);
    Route::put('questoes/{id}', [QuestaoController::class, 'update']);
    Route::delete('questoes/{id}', [QuestaoController::class, 'destroy']);
    Route::delete('questoes/opcoes/{id}', [QuestaoController::class, 'destroyOpcao']);
    Route::post('questoes/vinculos', [QuestaoController::class, 'vincularQuestaoQuestionario']);
    Route::delete('questoes/vinculos/{id}', [QuestaoController::class, 'destroyVinculo']);
    Route::post('questoes/respostas', [QuestaoController::class, 'storeResposta']);
});
