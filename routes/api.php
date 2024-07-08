<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

Route::post('register-new', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware(['auth.jwt'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::resource('users', UserController::class);
});




Route::post('test', function (Illuminate\Http\Request $request) {
    return response()->json(['message' => 'Test route working!', 'data' => $request->all()]);
});
