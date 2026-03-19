<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Traits\WithJwtAuth;
use Tests\TestCase;

class TransportTest extends TestCase
{
    use RefreshDatabase, WithJwtAuth;

    // =========================================================
    // GET /api/transportes
    // =========================================================

    public function test_listar_transportes_requiere_autenticacion()
    {
        $this->getJson('/api/transportes')->assertStatus(401);
    }

    public function test_listar_transportes_autenticado()
    {
        $headers = $this->actingAsRole('Traffic');

        $response = $this->withHeaders($headers)->getJson('/api/transportes');

        $response->assertStatus(200);
    }

    // =========================================================
    // POST /api/transporte
    // =========================================================

    public function test_crear_transporte_sin_datos_falla()
    {
        $headers = $this->actingAsRole('Traffic');

        $response = $this->withHeaders($headers)->postJson('/api/transporte', []);

        $response->assertStatus(422)
                 ->assertJson(['code' => 'VALIDATION_ERROR']);
    }

    public function test_crear_transporte_exitoso()
    {
        $headers = $this->actingAsRole('Traffic');

        $response = $this->withHeaders($headers)->postJson('/api/transporte', [
            'razon_social' => 'Transporte Test SA',
            'cuit'         => '20-' . rand(10000000, 99999999) . '-1',
            'empresa'      => 'empresa_test',
        ]);

        $response->assertStatus(200);
    }

    // =========================================================
    // GET /api/transporte/{id}
    // =========================================================

    public function test_ver_transporte_inexistente_devuelve_404()
    {
        $headers = $this->actingAsRole('Traffic');

        $response = $this->withHeaders($headers)->getJson('/api/transporte/999999');

        $response->assertStatus(404);
    }

    // =========================================================
    // GET /api/issetTransport/{cuit}
    // =========================================================

    public function test_isset_transporte_autenticado()
    {
        $headers = $this->actingAsRole('Traffic');

        $response = $this->withHeaders($headers)->getJson('/api/issetTransport/20-12345678-1');

        $response->assertStatus(200);
    }

    // =========================================================
    // Trucks
    // =========================================================

    public function test_listar_trucks_total_autenticado()
    {
        $headers = $this->actingAsRole('Traffic');

        $response = $this->withHeaders($headers)->getJson('/api/trucks');

        $response->assertStatus(200);
    }

    public function test_crear_truck_sin_datos_falla()
    {
        $headers = $this->actingAsRole('Traffic');

        $response = $this->withHeaders($headers)->postJson('/api/truck', []);

        $response->assertStatus(422)
                 ->assertJson(['code' => 'VALIDATION_ERROR']);
    }

    // =========================================================
    // Drivers
    // =========================================================

    public function test_listar_choferes_autenticado()
    {
        $headers = $this->actingAsRole('Traffic');

        $response = $this->withHeaders($headers)->getJson('/api/drivers');

        $response->assertStatus(200);
    }

    public function test_crear_chofer_sin_datos_falla()
    {
        $headers = $this->actingAsRole('Traffic');

        $response = $this->withHeaders($headers)->postJson('/api/driver', []);

        $response->assertStatus(422)
                 ->assertJson(['code' => 'VALIDATION_ERROR']);
    }
}
