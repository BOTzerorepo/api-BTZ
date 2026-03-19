<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Traits\WithJwtAuth;
use Tests\TestCase;

class CntrTest extends TestCase
{
    use RefreshDatabase, WithJwtAuth;

    // =========================================================
    // GET /api/cntr
    // =========================================================

    public function test_listar_cntrs_requiere_autenticacion()
    {
        $this->getJson('/api/cntr')->assertStatus(401);
    }

    public function test_listar_cntrs_autenticado()
    {
        $headers = $this->actingAsRole('Traffic');
        $this->withHeaders($headers)->getJson('/api/cntr')->assertStatus(200);
    }

    // =========================================================
    // GET /api/cntr/{id}
    // =========================================================

    public function test_ver_cntr_inexistente_devuelve_404()
    {
        $headers = $this->actingAsRole('Traffic');
        $this->withHeaders($headers)->getJson('/api/cntr/999999')->assertStatus(404);
    }

    // =========================================================
    // POST /api/cntr
    // =========================================================

    public function test_crear_cntr_sin_datos_falla()
    {
        $headers = $this->actingAsRole('Traffic');

        $response = $this->withHeaders($headers)->postJson('/api/cntr', []);

        $response->assertStatus(422)
                 ->assertJson(['code' => 'VALIDATION_ERROR']);
    }

    // =========================================================
    // GET /api/issetCntr/{cntr_number}
    // =========================================================

    public function test_isset_cntr_autenticado()
    {
        $headers = $this->actingAsRole('Traffic');

        $response = $this->withHeaders($headers)->getJson('/api/issetCntr/CNTR-TEST-001');

        $response->assertStatus(200);
    }

    // =========================================================
    // GET /api/status
    // =========================================================

    public function test_listar_status_autenticado()
    {
        $headers = $this->actingAsRole('Traffic');
        $this->withHeaders($headers)->getJson('/api/status')->assertStatus(200);
    }

    // =========================================================
    // GET /api/historialStatus/{cntr}
    // =========================================================

    public function test_historial_status_autenticado()
    {
        $headers = $this->actingAsRole('Traffic');

        $response = $this->withHeaders($headers)->getJson('/api/historialStatus/CNTR-TEST-001');

        $response->assertStatus(200);
    }

    // =========================================================
    // GET /api/instructivos/{user}
    // =========================================================

    public function test_listar_instructivos_autenticado()
    {
        $headers = $this->actingAsRole('Traffic');

        $response = $this->withHeaders($headers)->getJson('/api/instructivos/testuser');

        $response->assertStatus(200);
    }

    // =========================================================
    // Cntr Estado Resumen
    // =========================================================

    public function test_estado_resumen_cntr_autenticado()
    {
        $headers = $this->actingAsRole('Traffic');

        $response = $this->withHeaders($headers)->getJson('/api/cntr/estado-resumen');

        $response->assertStatus(200);
    }
}
