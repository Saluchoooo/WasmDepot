<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Tymon\JWTAuth\Http\Middleware\Authenticate as JWTAuth;



Route::get('users', [UserController::class, 'index']);

Route::get('items', [ItemController::class, 'index']);



Route::get('formulari', [ItemController::class, 'createItem'])->name('formulari');

Route::post('/submit-form', [ItemController::class, 'submit'])->name('form.submit');


//Auth




// Route::post('register', [AuthController::class, 'register']);
// Route::post('login', [AuthController::class, 'login']);
// Route::post('logout', [AuthController::class, 'logout']);
// Route::get('user', [AuthController::class, 'getAuthenticatedUser']);




