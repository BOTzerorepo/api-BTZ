<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Database\Console\Migrations\ResetCommand;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator; // Importa el Validator
use Tymon\JWTAuth\Facades\JWTAuth; // Importa el JWTAuth
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Spatie\Permission\Models\Role;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller 
{
    public function register(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'pass' => 'required|string|min:6|confirmed',
            'name' => 'nullable|string|max:20',
            'last_name' => 'nullable|string|max:29',
            'celular' => 'nullable|string',
            'empresa' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        

        try {
            $user = User::create([
                    'username' => $request->get('username'),
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

        if ($user && Hash::check($credentials['pass'], $user->pass)) {
            $token       = JWTAuth::fromUser($user);
            $role        = $user->getRoleNames()->first();
            $permissions = Role::findByName($role)->permissions->pluck('name')->toArray();

            return response()->json([
                'success'      => true,
                'token'        => $token,
                'id'           => $user->id,
                'username'     => $user->username,
                'email'        => $user->email,
                'company'      => $user->empresa,
                'role'         => $role,
                'permiso'      => $permissions,
                'transport_id' => $user->transport_id,
                'cliente_id'   => $user->cliente_id,
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'code'    => 'INVALID_CREDENTIALS',
                'message' => 'Usuario o contraseña incorrecto.',
            ], 401);
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

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $ruta = env('FRONT_URL');

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $token = Str::random(64);
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => Carbon::now()]
        );

        // Envía mail
        Mail::to($user->email)->send(new ResetPasswordMail($token, $ruta));

        return response()->json(['message' => 'Correo enviado']);
    }

    // Paso 2: Resetear contraseña
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|min:8|confirmed'
        ]);

        $reset = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$reset) {
            return response()->json(['message' => 'Token inválido o expirado'], 400);
        }

        $user = User::where('email', $request->email)->first();
        $user->pass = Hash::make($request->password);
        $user->save();

        // Borrar token
        DB::table('password_resets')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Contraseña actualizada correctamente']);
    }
}
