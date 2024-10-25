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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'pass' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'username' => $request->get('name'),
            'email' => $request->get('email'),
            'pass' => bcrypt($request->get('pass')),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user', 'token'), 201);
    }

    public function login(Request $request)
    {
        // Validar las credenciales
        $this->validate($request, [
            'email' => 'required|email',
            'pass' => 'required',
        ]);

        $credentials = $request->only('email', 'pass');

        // Encontrar el usuario
        $user = User::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['pass'], $user->pass)) {
            // Generar el token
            $token = JWTAuth::fromUser($user);

            // Devolver el token y el id del usuario
            return response()->json([
                'token' => $token,
                'user_id' => $user->id,
                'username' => $user->username
            ]);
        }

        // En caso de credenciales inválidas
        return response()->json(['error' => 'invalid_credentials'], 401);
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
