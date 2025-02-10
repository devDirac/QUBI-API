<?php

use App\Http\Controllers\API\ContratosController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\GeoJsonController;
use App\Http\Controllers\API\ProyectosController;

$SANCTUM = 'auth:sanctum';

/**RUTAS PARA EL MANEJO DEL ESTADO DE SESION Y COSAS RELEVANTES AL USUARIO */
Route::post('login', [AuthController::class, 'signin']);
Route::post('logOut', [AuthController::class, 'logOut'])->middleware($SANCTUM);
Route::post('register', [AuthController::class, 'signup'])->middleware($SANCTUM);
Route::get('getUserRefrsh/{id}', [AuthController::class, 'getUserRefrsh'])->middleware($SANCTUM);
Route::post('recuperaContrasena', [AuthController::class, 'passwordRecoverSendLink']);
Route::post('recuperaContrasenaTokenValidacion', [AuthController::class, 'passwordRecoverTokenValidation']);
Route::post('actualizacionContrasena', [AuthController::class, 'passwordReset']);
Route::get('getUsers', [AuthController::class, 'getUsers'])->middleware($SANCTUM);
Route::put('editUser', [AuthController::class, 'editUser'])->middleware($SANCTUM);
Route::post('setActiveUser', [AuthController::class, 'setActiveUser'])->middleware($SANCTUM);
Route::post('passwordResetSinToken', [AuthController::class, 'passwordResetSinToken'])->middleware($SANCTUM);


/* Geo database */
Route::post('convierteGeoJson', [GeoJsonController::class, 'convierteGeoJson']);
Route::post('setReporte', [GeoJsonController::class, 'setReporte'])->middleware($SANCTUM);
Route::post('deleteReporte', [GeoJsonController::class, 'deleteReporte'])->middleware($SANCTUM);
/* Juno proyectos */
Route::get('getAllProyectos', [ProyectosController::class, 'getAllProyectos'])->middleware($SANCTUM);
Route::post('storeProyecto', [ProyectosController::class, 'storeProyecto'])->middleware($SANCTUM);
Route::post('updateProyecto', [ProyectosController::class, 'updateProyecto'])->middleware($SANCTUM);
Route::delete('deleteProyecto', [ProyectosController::class, 'deleteProyecto'])->middleware($SANCTUM);
Route::post('asociaUsuarioProyecto', [ProyectosController::class, 'asociaUsuarioProyecto'])->middleware($SANCTUM);
Route::post('eliminaAsociacionUsuarioProyecto', [ProyectosController::class, 'eliminaAsociacionUsuarioProyecto'])->middleware($SANCTUM);