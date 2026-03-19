<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function createTrafficUser(string $password = 'password'): User
    {
        $user = User::factory()->create(['pass' => bcrypt($password)]);

        if (!Role::where('name', 'Traffic')->exists()) {
            Role::create(['name' => 'Traffic', 'guard_name' => 'api']);
        }

        $user->assignRole('Traffic');
        return $user;
    }

    // =========================================================
    // LOGIN
    // =========================================================

    public function test_login_exitoso_devuelve_token_y_campos_correctos()
    {
        $user = $this->createTrafficUser('secret123');

        $response = $this->postJson('/api/login', [
            'username' => $user->username,
            'pass'     => 'secret123',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'token',
                     'id',
                     'username',
                     'email',
                     'company',
                     'role',
                     'permiso',
                     'transport_id',
                     'cliente_id',
                 ])
                 ->assertJson([
                     'success'  => true,
                     'username' => $user->username,
                     'role'     => 'Traffic',
                 ]);

        $this->assertIsArray($response->json('permiso'));
    }

    public function test_login_con_credenciales_incorrectas_devuelve_error_estandar()
    {
        $user = $this->createTrafficUser('correct');

        $response = $this->postJson('/api/login', [
            'username' => $user->username,
            'pass'     => 'wrong_password',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'success' => false,
                     'code'    => 'INVALID_CREDENTIALS',
                 ])
                 ->assertJsonStructure(['success', 'code', 'message']);
    }

    public function test_login_con_usuario_inexistente_devuelve_error()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'usuario_que_no_existe',
            'pass'     => 'password',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'success' => false,
                     'code'    => 'INVALID_CREDENTIALS',
                 ]);
    }

    public function test_login_sin_pass_devuelve_error_de_validacion()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'alguien',
        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'code'    => 'VALIDATION_ERROR',
                 ])
                 ->assertJsonStructure(['errors']);
    }

    public function test_login_devuelve_permiso_como_array()
    {
        $user = $this->createTrafficUser();

        $response = $this->postJson('/api/login', [
            'username' => $user->username,
            'pass'     => 'password',
        ]);

        $response->assertStatus(201);
        $this->assertIsArray($response->json('permiso'));
    }

    public function test_login_devuelve_role_como_string()
    {
        $user = $this->createTrafficUser();

        $response = $this->postJson('/api/login', [
            'username' => $user->username,
            'pass'     => 'password',
        ]);

        $response->assertStatus(201);
        $this->assertIsString($response->json('role'));
        $this->assertEquals('Traffic', $response->json('role'));
    }

    // =========================================================
    // REGISTRO
    // =========================================================

    public function test_register_crea_usuario_y_devuelve_token()
    {
        $response = $this->postJson('/api/register', [
            'username'              => 'nuevo_usuario',
            'email'                 => 'nuevo@test.com',
            'pass'                  => 'password123',
            'pass_confirmation'     => 'password123',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'user', 'token']);
    }

    public function test_register_con_email_duplicado_falla()
    {
        $user = User::factory()->create(['email' => 'duplicado@test.com']);

        $response = $this->postJson('/api/register', [
            'username'          => 'otro_user',
            'email'             => 'duplicado@test.com',
            'pass'              => 'password123',
            'pass_confirmation' => 'password123',
        ]);

        $response->assertStatus(400);
    }

    // =========================================================
    // LOGOUT
    // =========================================================

    public function test_logout_requiere_autenticacion()
    {
        $response = $this->postJson('/api/logout');
        $response->assertStatus(401);
    }

    public function test_logout_exitoso()
    {
        $user  = $this->createTrafficUser();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
            'Accept'        => 'application/json',
        ])->postJson('/api/logout');

        $response->assertStatus(200);
    }

    // =========================================================
    // TOKEN
    // =========================================================

    public function test_request_sin_token_devuelve_401()
    {
        $response = $this->getJson('/api/user');
        $response->assertStatus(401);
    }

    public function test_request_con_token_invalido_devuelve_error_estandar()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer token_invalido_123',
            'Accept'        => 'application/json',
        ])->getJson('/api/user');

        $response->assertStatus(401)
                 ->assertJsonStructure(['success', 'code', 'message']);
    }

    public function test_get_authenticated_user_con_token_valido()
    {
        $user  = $this->createTrafficUser();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
            'Accept'        => 'application/json',
        ])->getJson('/api/user');

        $response->assertStatus(200)
                 ->assertJsonStructure(['user']);
    }
}
