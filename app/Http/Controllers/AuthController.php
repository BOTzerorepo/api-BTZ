<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator; // Importa el Validator
use Tymon\JWTAuth\Facades\JWTAuth; // Importa el JWTAuth
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthController extends Controller 
{
    public function register(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'pass' => 'required|string|min:6|confirmed',
            'name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'celular' => 'nullable|numeric',
            'empresa' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        

        try {
            $user = User::create([
                    'username' => $request->get('name'),
                    'email' => $request->get('email'),
                    'pass' => bcrypt($request->get('pass')),  
                    'name' => $request->get('name'),
                    'last_name' => $request->get('last_name'),
                    'celular' => $request->get('celular'),
                    'empresa' => $request->get('empresa')
                ]);

            // Generar el token JWT
            $token = JWTAuth::fromUser($user);

            return response()->json([
                'message' => 'Usuario creado exitosamente.',
                'user' => $user,
                'token' => $token
            ], 201);
        } catch (\Exception $e) {
          
            return response()->json([
                'message' => 'Hubo un error al crear el usuario.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'nullable|email',
            'username' => 'nullable|string',
            'pass' => 'required',
        ]);

        $credentials = $request->only('email', 'username', 'pass');

        // Encontrar el usuario por email o por username
        $user = null;

        if (!empty($credentials['email'])) {
            $user = User::where('email', $credentials['email'])->first();
        } elseif (!empty($credentials['username'])) {
            $user = User::where('username', $credentials['username'])->first();
        }

        if ($user && Hash::check($credentials['pass'], $user->pass)
        ) {
            // Generar el token
            $token = JWTAuth::fromUser($user);

            // Devolver el token y la información del usuario
            return response()->json([
                'token' => $token,
                'user_id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'company' => $user->empresa,
                'permiso' => $user->permiso,
                'transport_id' => $user->transport_id,
            ], 201);
        } else {
            return response()->json(['error' => 'Usuario o contraseña incorrecto.'], 400);
        }
    }


    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function getAuthenticatedUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'token_expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'token_invalid'], 401);
        } catch (JWTException $e) {
            return response()->json(['error' => 'token_absent'], 400);
        }

        return response()->json(compact('user'));
    }
}
