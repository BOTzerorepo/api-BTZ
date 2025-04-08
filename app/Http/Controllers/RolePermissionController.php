<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class RolePermissionController extends Controller
{

    // ------------------PERMISOS----------------------------
    /**Crear un nuevo permiso.*/
    public function createPermission(Request $request)
    {
        try {
            // Validar entrada
            $request->validate([
                'name' => 'required|string|unique:permissions,name', // Nombre único
            ]);

            // Crear el permiso
            $permission = Permission::create(['name' => $request->name]);

            // Respuesta en caso de éxito
            return response()->json([
                'message' => 'Permiso creado exitosamente.',
                'permission' => $permission
            ], 201);
        } catch (ValidationException $e) {
            // Errores de validación
            return response()->json([
                'error' => 'Error de validación',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Otros errores (por ejemplo, problemas con la base de datos)
            return response()->json([
                'error' => 'Error interno del servidor',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function editPermission(Request $request, $id)
    {
        try {
            // Validar entrada
            $request->validate([
                'name' => 'required|string|unique:permissions,name,' . $id, // Nombre único excepto para el permiso actual
            ]);

            // Buscar el permiso por ID
            $permission = Permission::findOrFail($id);

            // Actualizar el nombre del permiso
            $permission->name = $request->name;
            $permission->save();

            // Respuesta en caso de éxito
            return response()->json([
                'message' => 'Permiso actualizado exitosamente.',
                'permission' => $permission
            ], 200);
        } catch (ValidationException $e) {
            // Errores de validación
            return response()->json([
                'error' => 'Error de validación',
                'details' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            // Si el permiso no existe
            return response()->json([
                'error' => 'Permiso no encontrado.',
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            // Otros errores
            return response()->json([
                'error' => 'Error interno del servidor',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**Obtener todos los permisos.*/
    public function getPermissions()
    {
        try {
            $permissions = Permission::all();
            return response()->json($permissions);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error interno del servidor', 'message' => $e->getMessage()], 500);
        }
    }

    // ------------------ROLES----------------------------
    /**Crear un nuevo rol y asignar permisos.*/
    public function createRole(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:roles,name',
                'permissions' => 'array',
                'permissions.*' => 'exists:permissions,name',
            ]);

            $role = Role::create(['name' => $request->name]);

            if ($request->has('permissions')) {
                $role->givePermissionTo($request->permissions);
            }

            return response()->json(['message' => 'Rol creado exitosamente.', 'role' => $role], 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Error de validación', 'details' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error interno del servidor', 'message' => $e->getMessage()], 500);
        }
    }
    public function updateRole(Request $request, $id)
    {
        try {
            // Validar los datos de entrada
            $request->validate([
                'name' => 'string|unique:roles,name,' . $id,
                'permissions' => 'array',
                'permissions.*' => 'exists:permissions,name',
            ]);

            // Buscar el rol por su ID
            $role = Role::findOrFail($id);

            // Actualizar el nombre del rol si es proporcionado
            if ($request->has('name')) {
                $role->name = $request->name;
                $role->save();
            }

            // Actualizar los permisos del rol
            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            return response()->json([
                'message' => 'Rol actualizado exitosamente.',
                'role' => $role,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Error de validación', 'details' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Rol no encontrado', 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error interno del servidor', 'message' => $e->getMessage()], 500);
        }
    }
    public function deleteRole($id)
    {
        try {
            // Obtener el rol por ID
            $role = Role::findOrFail($id);

            // Verificar si algún usuario tiene asignado este rol
            if ($role->users()->exists()) {
                return response()->json([
                    'message' => 'No se puede eliminar el rol porque está asignado a uno o más usuarios.',
                ], 400);
            }

            // Eliminar el rol
            $role->delete();

            return response()->json([
                'message' => 'Rol eliminado exitosamente.',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el rol.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /*Obtener todos los roles.*/
    public function getRoles()
    {
        try {
            $roles = Role::with('permissions')->get();
            return response()->json($roles);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error interno del servidor', 'message' => $e->getMessage()], 500);
        }
    }

    public function deletePermission($id)
    {
        try {
            // Buscar el permiso por ID
            $permission = Permission::findOrFail($id);

            // Verificar si el permiso está asociado a algún rol
            if ($permission->roles()->exists()) {
                return response()->json([
                    'error' => 'El permiso no se puede eliminar porque está asociado a uno o más roles.'
                ], 400);
            }

            // Eliminar el permiso
            $permission->delete();

            // Respuesta en caso de éxito
            return response()->json([
                'message' => 'Permiso eliminado exitosamente.'
            ], 200);
        } catch (ModelNotFoundException $e) {
            // Si el permiso no existe
            return response()->json([
                'error' => 'Permiso no encontrado.',
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            // Otros errores
            return response()->json([
                'error' => 'Error interno del servidor',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**Asignar permisos a un rol.*/
    public function assignPermissionsToRole(Request $request)
    {
        try {
            $request->validate([
                'role_id' => 'required|exists:roles,id',
                'permissions' => 'required|array',
                'permissions.*' => 'exists:permissions,name',
            ]);

            $role = Role::findOrFail($request->role_id);
            $role->givePermissionTo($request->permissions);

            return response()->json(['message' => 'Permisos asignados al rol correctamente.']);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Error de validación', 'details' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error interno del servidor', 'message' => $e->getMessage()], 500);
        }
    }

    // ------------------ROLES Y PERMISOS ASOCIADOS A USUARIOS----------------------------
    /**Asignar un rol a un usuario.*/
    public function assignRoleToUser(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'role' => 'required|exists:roles,name',
            ]);

            $user = User::findOrFail($request->user_id);

            // Eliminar todos los roles actuales del usuario
            $user->roles()->detach();

            // Asignar el nuevo rol
            $user->assignRole($request->role);

            return response()->json(['message' => 'Rol asignado al usuario correctamente.']);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Error de validación', 'details' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error interno del servidor', 'message' => $e->getMessage()], 500);
        }
    }

    /**Asignar permisos directamente a un usuario.*/
    public function assignPermissionToUser(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'permissions' => 'array',
                'permissions.*' => 'exists:permissions,name',
            ]);

            $user = User::findOrFail($request->user_id);
            $user->givePermissionTo($request->permissions);

            return response()->json(['message' => 'Permisos asignados al usuario correctamente.']);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Error de validación', 'details' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error interno del servidor', 'message' => $e->getMessage()], 500);
        }
    }
    /**Obtener los permisos de un rol.*/
    public function getPermissionsByRole($roleId)
    {
        try {
            $role = Role::with('permissions')->findOrFail($roleId);
            return response()->json($role->permissions);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error interno del servidor', 'message' => $e->getMessage()], 500);
        }
    }

    /**Remover un rol de un usuario.*/
    public function removeRoleFromUser(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'role' => 'required|exists:roles,name',
            ]);

            $user = User::findOrFail($request->user_id);
            $user->removeRole($request->role);

            return response()->json(['message' => 'Rol removido correctamente.']);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Error de validación', 'details' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error interno del servidor', 'message' => $e->getMessage()], 500);
        }
    }

    /**Sincronizar roles de un usuario.*/
    public function syncRolesForUser(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'roles' => 'required|array',
                'roles.*' => 'exists:roles,name',
            ]);

            $user = User::findOrFail($request->user_id);
            $user->syncRoles($request->roles);

            return response()->json(['message' => 'Roles sincronizados correctamente.']);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Error de validación', 'details' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error interno del servidor', 'message' => $e->getMessage()], 500);
        }
    }
    public function getUserRoleAndPermissions($userId)
    {
        // Obtener el usuario por ID
        $user = User::findOrFail($userId);

        // Obtener el rol del usuario (asumiendo que cada usuario tiene un solo rol)
        $role = $user->getRoleNames()->first();

        // Obtener los permisos del rol
        $permissions = Role::findByName($role)->permissions;

        // Devolver el rol y los permisos del rol
        return [
            'role' => $role,
            'permissions' => $permissions->pluck('name')
        ];
    }
}
