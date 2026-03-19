<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\Feature\Traits\WithJwtAuth;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, WithJwtAuth;

    // =========================================================
    // GET /api/users
    // =========================================================

    public function test_listar_usuarios_requiere_autenticacion()
    {
        $this->getJson('/api/users')->assertStatus(401);
    }

    public function test_listar_usuarios_autenticado()
    {
        $headers = $this->actingAsRole('Master');
        User::factory()->count(3)->create();

        $response = $this->withHeaders($headers)->getJson('/api/users');

        $response->assertStatus(200);
    }

    // =========================================================
    // GET /api/user/{id}
    // =========================================================

    public function test_ver_usuario_existente()
    {
        $headers = $this->actingAsRole('Master');
        $user    = User::factory()->create();

        $response = $this->withHeaders($headers)->getJson("/api/user/{$user->id}");

        $response->assertStatus(200);
    }

    public function test_ver_usuario_inexistente_devuelve_404()
    {
        $headers = $this->actingAsRole('Master');

        $response = $this->withHeaders($headers)->getJson('/api/user/999999');

        $response->assertStatus(404);
    }

    // =========================================================
    // PUT /api/user/{id}
    // =========================================================

    public function test_actualizar_usuario()
    {
        $headers = $this->actingAsRole('Master');
        $user    = User::factory()->create();

        $response = $this->withHeaders($headers)->putJson("/api/user/{$user->id}", [
            'name'      => 'Nombre Actualizado',
            'last_name' => 'Apellido',
            'email'     => $user->email,
            'username'  => $user->username,
        ]);

        $response->assertStatus(200);
    }

    // =========================================================
    // DELETE /api/user/{id}
    // =========================================================

    public function test_eliminar_usuario()
    {
        $headers = $this->actingAsRole('Master');
        $user    = User::factory()->create();

        $response = $this->withHeaders($headers)->deleteJson("/api/user/{$user->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    // =========================================================
    // GET /api/users/without-role
    // =========================================================

    public function test_usuarios_sin_rol()
    {
        $headers = $this->actingAsRole('Master');
        User::factory()->count(2)->create();

        $response = $this->withHeaders($headers)->getJson('/api/users/without-role');

        $response->assertStatus(200);
    }

    // =========================================================
    // Roles y permisos
    // =========================================================

    public function test_asignar_rol_a_usuario()
    {
        $headers = $this->actingAsRole('Master');
        $user    = User::factory()->create();

        if (!Role::where('name', 'Customer')->exists()) {
            Role::create(['name' => 'Customer', 'guard_name' => 'api']);
        }

        $response = $this->withHeaders($headers)->postJson('/api/users/assign-role', [
            'user_id'   => $user->id,
            'role_name' => 'Customer',
        ]);

        $response->assertStatus(200);
    }

    public function test_obtener_roles()
    {
        $headers = $this->actingAsRole('Master');

        $response = $this->withHeaders($headers)->getJson('/api/roles');

        $response->assertStatus(200);
    }

    public function test_obtener_permisos()
    {
        $headers = $this->actingAsRole('Master');

        $response = $this->withHeaders($headers)->getJson('/api/permissions');

        $response->assertStatus(200);
    }
}
