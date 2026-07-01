<?php

namespace App\Http\Controllers;

use App\Models\AccionComercial;
use App\Models\ClienteComercial;
use App\Models\ProximaAccion;
use App\Models\User;
use App\Models\UsuarioClienteAcceso;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ClienteComercialController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        try {
            $user = auth('api')->user();
            $query = ClienteComercial::query();

            if (!$user->hasRole('AdminComercial')) {
                $query->where('empresa', $user->empresa);
            }

            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }
            if ($request->filled('segmento')) {
                $query->where('segmento', $request->segmento);
            }
            if ($request->filled('q')) {
                $query->where('razon_social', 'like', '%' . $request->q . '%');
            }

            return $this->success($query->orderBy('razon_social')->get());
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $cliente = ClienteComercial::with([
                'acciones',
                'proximasAcciones',
                'sucursales.usuarios',
            ])->findOrFail($id);

            return $this->success($cliente);
        } catch (\Exception $e) {
            return $this->notFound('Cliente no encontrado.');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'razon_social'       => 'required|string|max:255',
                'cuit'               => 'nullable|string|max:20',
                'industria'          => 'nullable|string|max:255',
                'segmento'           => 'nullable|in:A,B,C',
                'estado'             => 'nullable|in:Activo,Inactivo,Prospecto',
                'fecha_alta'         => 'nullable|date',
                'contacto_nombre'    => 'nullable|string|max:255',
                'contacto_email'     => 'nullable|email|max:255',
                'contacto_telefono'  => 'nullable|string|max:50',
                'contacto_cargo'     => 'nullable|string|max:255',
                'direccion'          => 'nullable|string|max:255',
                'notas'              => 'nullable|string',
            ]);

            $validated['empresa'] = auth('api')->user()->empresa;
            $cliente = ClienteComercial::create($validated);

            return $this->created($cliente, 'Cliente creado.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Error de validación.', 'VALIDATION_ERROR', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $cliente = ClienteComercial::findOrFail($id);

            $validated = $request->validate([
                'razon_social'       => 'sometimes|string|max:255',
                'cuit'               => 'nullable|string|max:20',
                'industria'          => 'nullable|string|max:255',
                'segmento'           => 'nullable|in:A,B,C',
                'estado'             => 'nullable|in:Activo,Inactivo,Prospecto',
                'fecha_alta'         => 'nullable|date',
                'contacto_nombre'    => 'nullable|string|max:255',
                'contacto_email'     => 'nullable|email|max:255',
                'contacto_telefono'  => 'nullable|string|max:50',
                'contacto_cargo'     => 'nullable|string|max:255',
                'direccion'          => 'nullable|string|max:255',
                'notas'              => 'nullable|string',
            ]);

            $cliente->update($validated);

            return $this->success($cliente, 'Cliente actualizado.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Error de validación.', 'VALIDATION_ERROR', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            ClienteComercial::findOrFail($id)->delete();
            return $this->success(null, 'Cliente eliminado.');
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    // ── Acciones comerciales ─────────────────────────────────────────────────

    public function storeAccion(Request $request, $clienteId)
    {
        try {
            ClienteComercial::findOrFail($clienteId);

            $validated = $request->validate([
                'fecha'       => 'required|date',
                'tipo'        => 'required|in:Llamada,Reunión,Email,Propuesta,Seguimiento',
                'descripcion' => 'required|string',
                'resultado'   => 'nullable|string',
            ]);

            $validated['cliente_id'] = $clienteId;
            $accion = AccionComercial::create($validated);

            return $this->created($accion, 'Acción registrada.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Error de validación.', 'VALIDATION_ERROR', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function destroyAccion($id)
    {
        try {
            AccionComercial::findOrFail($id)->delete();
            return $this->success(null, 'Acción eliminada.');
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    // ── Próximas acciones ────────────────────────────────────────────────────

    public function storeProximaAccion(Request $request, $clienteId)
    {
        try {
            ClienteComercial::findOrFail($clienteId);

            $validated = $request->validate([
                'fecha'        => 'required|date',
                'tipo'         => 'required|string|max:100',
                'descripcion'  => 'required|string',
                'responsable'  => 'required|string|max:100',
            ]);

            $validated['cliente_id'] = $clienteId;
            $proxima = ProximaAccion::create($validated);

            return $this->created($proxima, 'Próxima acción registrada.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Error de validación.', 'VALIDATION_ERROR', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function completarProximaAccion($id)
    {
        try {
            $proxima = ProximaAccion::findOrFail($id);
            $proxima->update(['completada' => true]);
            return $this->success($proxima, 'Acción marcada como completada.');
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function destroyProximaAccion($id)
    {
        try {
            ProximaAccion::findOrFail($id)->delete();
            return $this->success(null, 'Próxima acción eliminada.');
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    // ── Usuarios del sistema vinculados ──────────────────────────────────────

    public function usuariosSistema($clienteId)
    {
        try {
            ClienteComercial::findOrFail($clienteId);

            $accesos = UsuarioClienteAcceso::with('user')
                ->where('cliente_comercial_id', $clienteId)
                ->get()
                ->map(function ($acceso) {
                    return [
                        'id'                   => $acceso->id,
                        'user_id'              => $acceso->user_id,
                        'username'             => $acceso->user->username ?? '',
                        'name'                 => trim(($acceso->user->name ?? '') . ' ' . ($acceso->user->last_name ?? '')),
                        'email'                => $acceso->user->email ?? '',
                        'permiso'              => $acceso->user->permiso ?? '',
                        'ver_precios'          => $acceso->ver_precios,
                        'ver_documentos'       => $acceso->ver_documentos,
                        'ver_tracking'         => $acceso->ver_tracking,
                        'ver_cargas_internas'  => $acceso->ver_cargas_internas,
                        'notif_email'          => $acceso->notif_email,
                        'notif_nuevas_cargas'  => $acceso->notif_nuevas_cargas,
                        'notif_cambio_estado'  => $acceso->notif_cambio_estado,
                        'columnas_visibles'    => $acceso->columnas_visibles,
                        'notas'                => $acceso->notas,
                    ];
                });

            return $this->success($accesos);
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function vincularUsuario(Request $request, $clienteId)
    {
        try {
            ClienteComercial::findOrFail($clienteId);

            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);

            $acceso = UsuarioClienteAcceso::firstOrCreate(
                ['cliente_comercial_id' => $clienteId, 'user_id' => $validated['user_id']],
                [
                    'ver_precios'         => false,
                    'ver_documentos'      => true,
                    'ver_tracking'        => true,
                    'ver_cargas_internas' => false,
                    'notif_email'         => false,
                    'notif_nuevas_cargas' => false,
                    'notif_cambio_estado' => true,
                ]
            );

            $acceso->load('user');

            return $this->created([
                'id'                   => $acceso->id,
                'user_id'              => $acceso->user_id,
                'username'             => $acceso->user->username ?? '',
                'name'                 => trim(($acceso->user->name ?? '') . ' ' . ($acceso->user->last_name ?? '')),
                'email'                => $acceso->user->email ?? '',
                'permiso'              => $acceso->user->permiso ?? '',
                'ver_precios'          => $acceso->ver_precios,
                'ver_documentos'       => $acceso->ver_documentos,
                'ver_tracking'         => $acceso->ver_tracking,
                'ver_cargas_internas'  => $acceso->ver_cargas_internas,
                'notif_email'          => $acceso->notif_email,
                'notif_nuevas_cargas'  => $acceso->notif_nuevas_cargas,
                'notif_cambio_estado'  => $acceso->notif_cambio_estado,
                'columnas_visibles'    => $acceso->columnas_visibles,
                'notas'                => $acceso->notas,
            ], 'Usuario vinculado.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Error de validación.', 'VALIDATION_ERROR', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function desvinularUsuario($clienteId, $userId)
    {
        try {
            UsuarioClienteAcceso::where('cliente_comercial_id', $clienteId)
                ->where('user_id', $userId)
                ->firstOrFail()
                ->delete();

            return $this->success(null, 'Usuario desvinculado.');
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function actualizarPreferencias(Request $request, $clienteId, $userId)
    {
        try {
            $acceso = UsuarioClienteAcceso::where('cliente_comercial_id', $clienteId)
                ->where('user_id', $userId)
                ->firstOrFail();

            $validated = $request->validate([
                'ver_precios'          => 'sometimes|boolean',
                'ver_documentos'       => 'sometimes|boolean',
                'ver_tracking'         => 'sometimes|boolean',
                'ver_cargas_internas'  => 'sometimes|boolean',
                'notif_email'          => 'sometimes|boolean',
                'notif_nuevas_cargas'  => 'sometimes|boolean',
                'notif_cambio_estado'  => 'sometimes|boolean',
                'columnas_visibles'    => 'sometimes|array',
                'notas'                => 'nullable|string',
            ]);

            $acceso->update($validated);

            return $this->success($acceso, 'Preferencias actualizadas.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Error de validación.', 'VALIDATION_ERROR', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    // ── Búsqueda de usuarios para vincular ───────────────────────────────────

    public function buscarUsuariosSistema(Request $request, $clienteId)
    {
        try {
            $q       = $request->input('q', '');
            $permiso = $request->input('permiso', '');

            $yaVinculados = UsuarioClienteAcceso::where('cliente_comercial_id', $clienteId)
                ->pluck('user_id');

            $query = User::whereNotIn('id', $yaVinculados);

            if ($permiso) {
                $query->where('permiso', $permiso);
            } else {
                $query->whereIn('permiso', ['ClienteEmpresa', 'Customer', 'Comercial']);
            }

            if ($q) {
                $query->where(function ($q2) use ($q) {
                    $q2->where('username', 'like', "%{$q}%")
                       ->orWhere('email', 'like', "%{$q}%")
                       ->orWhere('name', 'like', "%{$q}%");
                });
            }

            $users = $query->limit(20)->get(['id', 'username', 'name', 'last_name', 'email', 'permiso', 'empresa']);

            return $this->success($users);
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }
}
