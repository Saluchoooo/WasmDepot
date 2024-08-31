<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;


Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');

Route::post('/getUser', [AuthController::class, 'getAuthenticatedUser'])->middleware('auth:api');

Route::post('/register', [AuthController::class, 'register']);

Route::post('/newItem', [ItemController::class, 'submit'])->middleware('auth:api');

Route::get('/list', [ItemController::class, 'index']);

Route::get('/items/{id}', [ItemController::class, 'show']);

Route::put('/items/{id}/favorites', [AuthController::class, 'updateFavorites'])->middleware('auth:api');

Route::post('/items/{id}/installation', [ItemController::class, 'saveInstallation'])->middleware('auth:api');

Route::post('/items/{id}/screenshots', [ItemController::class, 'saveScreenshots'])->middleware('auth:api');

Route::post('/items/{id}/benchmarkText', [ItemController::class, 'saveBenchmark'])->middleware('auth:api');

Route::post('/items/{id}/benchmarkImage', [ItemController::class, 'uploadBenchmark'])->middleware('auth:api');

Route::delete('/items/{id}', [ItemController::class, 'destroy'])->middleware('auth:api');




