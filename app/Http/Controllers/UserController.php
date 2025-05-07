<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $users = User::all();
            return response()->json([
                'data' => $users,
                'success' => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error interno del servidor',
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function indexNullPermiso()
    {
        try {
            $users = User::whereNull('permiso')->get();
            return response()->json([
                'data' => $users,
                'success' => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error interno del servidor',
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function usersWithoutRole()
    {
        try {
            $users = User::whereNull('permiso')->count();

            return response()->json([
                'success' => true,
                'data' => $users
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $user = DB::table('users')
                ->join('empresas', 'empresas.razon_social', '=', 'users.Empresa')
                ->where('users.username', $id)
                ->select('users.*', 'empresas.*')
                ->first();
            return response()->json([
                'success' => true,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al calcular el profit.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'username' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'celular' => 'nullable|string|max:20',
                'empresa' => 'nullable|string|max:255',
                'permiso' => 'required|string|max:50',
                'transport_id' => 'nullable',
            ]);

            $user = User::findOrFail($id);
            $user->username = $request->input('username');
            $user->email = $request->input('email');
            $user->celular = $request->input('celular');
            $user->empresa = $request->input('empresa');
            $user->permiso = $request->input('permiso');

            // Si transport_id es array, lo convertimos en string separado por coma
            $transport_id = $request->input('transport_id');
            if (is_array($transport_id)) {
                $user->transport_id = implode(',', $transport_id);
            } else {
                $user->transport_id = $transport_id;
            }
            $user->save();

            return response()->json([
                'message' => 'Usuario actualizado correctamente.',
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            // 6. Error general
            return response()->json([
                'message' => 'Error al actualizar el usuario.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $deleted = User::destroy($id);

            if ($deleted) {
                return response()->json([
                    'message' => 'El usuario fue eliminada correctamente.'
                ], 200);
            } else {
                return response()->json([
                    'message' => 'No se encontró el usuario o no se pudo eliminar.'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al intentar eliminar el usuario.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
