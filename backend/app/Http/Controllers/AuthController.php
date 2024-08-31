<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Support\Facades\Log;



class AuthController extends Controller
{
    // Funció per a registrar-se
    public function register(Request $request)
    {
        // Validació de les dades d'entrada
        Log::info('Dades rebudes:', $request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|max:50',
        ]);


        if ($validator->fails()) {
            $errors=$validator->errors();
            Log::error('Errors de validació:', $errors->toArray());
            return response()->json($validator->errors(), 422);
        }

        // Creació d'un nou usuari
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'favorites'=> []

        ]);

        // Generació d'un token JWT
        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user', 'token'), 201);
    }

    // Funció per a iniciar sessió
    public function login(Request $request)
    {
        // Credencials d'entrada
        $credentials = $request->only('email', 'password');
        Log::error('valors:', $credentials);
        try {
            // Intent d'iniciar sessió
            if (!$token = JWTAuth::attempt($credentials)) {
                
                return response()->json(['error' => 'Invalid credentials'], 401);
            }

        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        // Retorna el token si l'autenticació és correcta
        return response()->json(compact('token'), 201);
    }


    // Funció per a tancar sessió
    public function logout()
    {
        // Invalidar el token
        //Aquí s'hauria de invalidar el token pero resulta que la base de dades MongoDB no dona suport a upsert i no deix invalidar.
        //JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Successfully logged out']);
    }

    // Funció per obtenir l'usuari autenticat
    public function getAuthenticatedUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['error' => 'user_not_found'], 404);
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'token_expired'], 401); // 401 Unauthorized
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'token_invalid'], 401); // 401 Unauthorized
        } catch (JWTException $e) {
            return response()->json(['error' => 'token_absent'], 401); // 401 Unauthorized
        }

        return response()->json(compact('user'));
    }

    public function updateFavorites(Request $request, $id)
    {
        $user = User::find($id);

        $request->validate([
            'favorites' => 'required|array', 
            'favorites.*' => 'string',
        ]);

        $user->favorites = $request->input('favorites');
        $user->save();

        return response()->json(['favorites' => $user->favorites]);
    }


}
