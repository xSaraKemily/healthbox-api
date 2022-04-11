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

    Route::post('likes', [LikeController::class, 'store']);
    Route::delete('likes',  [LikeController::class, 'destroy']);

    Route::post('crms', [MedicoCrmController::class, 'store']);
    Route::put('crms/{id}', [MedicoCrmController::class, 'update']);
    Route::delete('crms/{id}', [MedicoCrmController::class, 'destroy']);

    Route::post('especializacoes', [EspecializacaoController::class, 'store']);
    Route::delete('especializacoes/{id}', [EspecializacaoController::class, 'destroy']);

    Route::get('remedios', [RemedioController::class, 'index']);

    Route::get('opinioes', [OpiniaoController::class, 'index']);
    Route::post('opinioes', [OpiniaoController::class, 'store']);
    Route::put('opinioes/{id}', [OpiniaoController::class, 'update']);
    Route::delete('opinioes/{id}', [OpiniaoController::class, 'destroy']);

    Route::post('tratamentos', [TratamentoController::class, 'store']);
    Route::put('tratamentos', [TratamentoController::class, 'update']);
    Route::delete('tratamentos', [TratamentoController::class, 'destroy']);
});
