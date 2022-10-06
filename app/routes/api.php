<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('login', [App\Http\Controllers\Api\LoginController::class, 'login']);

Route::apiResource('usuario', App\Http\Controllers\Api\UsuarioController::class)->only(['store']);
Route::apiResource('sede', App\Http\Controllers\Api\SedeController::class);
Route::apiResource('estadio', App\Http\Controllers\Api\EstadioController::class);
Route::apiResource('liga', App\Http\Controllers\Api\LigaController::class);
Route::apiResource('grupo', App\Http\Controllers\Api\GrupoController::class);
Route::apiResource('seleccion-pais', App\Http\Controllers\Api\SeleccionPaisController::class);
Route::apiResource('partido', App\Http\Controllers\Api\PartidoController::class);
Route::apiResource('vaticinio', App\Http\Controllers\Api\VaticinioController::class);

Route::post('liga/{id}/copiar', [App\Http\Controllers\Api\LigaController::class, 'copiarLiga']);
Route::post('liga/{id}/enviar-invitaciones', [App\Http\Controllers\Api\LigaController::class, 'enviarInvitaciones']);
Route::post('liga/{id}/enviar-solicitud', [App\Http\Controllers\Api\LigaController::class, 'enviarSolicitud']);

Route::put('liga-usuario/{id}/rechazar', [App\Http\Controllers\Api\LigaUsuarioController::class, 'rechazar']);
Route::put('liga-usuario/{id}/aprobar', [App\Http\Controllers\Api\LigaUsuarioController::class, 'aprobar']);
Route::put('liga-usuario/{id}/pagar', [App\Http\Controllers\Api\LigaUsuarioController::class, 'pagar']);


Route::get('premiacion/{id_liga}/general', [App\Http\Controllers\Api\PremiacionController::class, 'obtenerPremiacionLigaCentral']);
