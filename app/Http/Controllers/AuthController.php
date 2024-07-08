<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            // Verificar que los datos llegan correctamente
            $validatedData = $request->validate([
                'name' => 'required|string|max:55',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed'
            ]);


            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password'])
            ]);


            $token = JWTAuth::fromUser($user);


            return response()->json(['token' => $token], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Capturar errores de validación
            $errors = $e->validator->errors();
            return response()->json(['errors' => $errors], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            // Capturar errores de base de datos, como duplicados
            if ($e->errorInfo[1] == 1062) {
                // Código de error 1062 es para entradas duplicadas en MySQL
                return response()->json(['error' => 'El usuario ya existe'], 409);
            }
            return response()->json(['error' => 'Error en la base de datos'], 500);
        } catch (\Exception $e) {
            // Capturar cualquier otra excepción
            return response()->json(['error' => 'Ocurrió un error inesperado'], 500);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');


        if (!$token = JWTAuth::attempt($credentials)) {

            $data= [
                'message' => 'Contraseña o correo son incorrectos por favor revisa',
                'response' => 'ok',
                'status' => 401,


            ];

            return response()->json($data, 401);
        }

        return response()->json(['token' => $token]);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['Message' => 'Successfully logged out']);
    }

    public function me()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json(['user_not_found'], 404);
            }

            return response()->json($user);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }
    }
}


