<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::post('test', function (Request $request) {
    return response()->json(['message' => 'Test route working!', 'data' => $request->all()]);
});
