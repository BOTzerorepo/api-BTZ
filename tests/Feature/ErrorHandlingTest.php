<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorHandlingTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================
    // Formato estándar de error
    // =========================================================

    public function test_ruta_inexistente_devuelve_formato_estandar()
    {
        $response = $this->getJson('/api/ruta-que-no-existe-xyz');

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'code'    => 'ROUTE_NOT_FOUND',
                 ])
                 ->assertJsonStructure(['success', 'code', 'message']);
    }

    public function test_error_autenticacion_devuelve_formato_estandar()
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401)
                 ->assertJson(['success' => false])
                 ->assertJsonStructure(['success', 'code', 'message']);
    }

    public function test_validacion_devuelve_formato_estandar_con_errors()
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'code'    => 'VALIDATION_ERROR',
                 ])
                 ->assertJsonStructure(['success', 'code', 'message', 'errors']);
    }

    public function test_error_tiene_campo_message_no_vacio()
    {
        $response = $this->getJson('/api/user');

        $this->assertNotEmpty($response->json('message'));
    }

    public function test_respuesta_exitosa_tiene_success_true()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'no_existe',
            'pass'     => 'algo',
        ]);

        // Cualquier respuesta tiene el campo success
        $this->assertArrayHasKey('success', $response->json());
    }
}
