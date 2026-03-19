<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Traits\WithJwtAuth;
use Tests\TestCase;

/**
 * Tests para los endpoints de datos maestros:
 * Agencias, ATAs, Aduanas, Commodities, Tipos de Cntr,
 * Modos/Plazos de Pago, Lugares de Carga/Descarga,
 * Traders, Shippers, Consignees, Notifies
 */
class MaestrosTest extends TestCase
{
    use RefreshDatabase, WithJwtAuth;

    // =========================================================
    // Agencias
    // =========================================================

    public function test_listar_agencias()
    {
        $headers = $this->actingAsRole('Traffic');
        $this->withHeaders($headers)->getJson('/api/agencias')->assertStatus(200);
    }

    public function test_crear_agencia_sin_datos_falla()
    {
        $headers = $this->actingAsRole('Traffic');
        $this->withHeaders($headers)->postJson('/api/agencia', [])
             ->assertStatus(422)->assertJson(['code' => 'VALIDATION_ERROR']);
    }

    public function test_ver_agencia_inexistente()
    {
        $headers = $this->actingAsRole('Traffic');
        $this->withHeaders($headers)->getJson('/api/agencia/999999')->assertStatus(404);
    }

    // =========================================================
    // ATAs
    // =========================================================

    public function test_listar_atas()
    {
        $headers = $this->actingAsRole('Traffic');
        $this->withHeaders($headers)->getJson('/api/atas')->assertStatus(200);
    }

    public function test_crear_ata_sin_datos_falla()
    {
        $headers = $this->actingAsRole('Traffic');
        $this->withHeaders($headers)->postJson('/api/ata', [])
             ->assertStatus(422)->assertJson(['code' => 'VALIDATION_ERROR']);
    }

    // =========================================================
    // Aduanas
    // =========================================================

    public function test_listar_aduanas()
    {
        $headers = $this->actingAsRole('Traffic');
        $this->withHeaders($headers)->getJson('/api/aduanas')->assertStatus(200);
    }

    // =========================================================
    // Commodities
    // =========================================================

    public function test_listar_commodities()
    {
        $headers = $this->actingAsRole('Traffic');
        $this->withHeaders($headers)->getJson('/api/commodities')->assertStatus(200);
    }

    // =========================================================
    // Tipos de Cntr
    // =========================================================

    public function test_listar_tipos_cntr()
    {
        $headers = $this->actingAsRole('Traffic');
        $this->withHeaders($headers)->getJson('/api/tiposCntr')->assertStatus(200);
    }

    // =========================================================
    // Modos de Pago
    // =========================================================

    public function test_listar_modos_de_pago()
    {
        $headers = $this->actingAsRole('Traffic');
        $this->withHeaders($headers)->getJson('/api/modoPagos')->assertStatus(200);
    }

    public function test_ver_modo_de_pago_inexistente()
    {
        $headers = $this->actingAsRole('Traffic');
        $this->withHeaders($headers)->getJson('/api/modoPago/999999')->assertStatus(404);
    }

    // =========================================================
    // Plazos de Pago
    // =========================================================

    public function test_listar_plazos_de_pago()
    {
        $headers = $this->actingAsRole('Traffic');
        $this->withHeaders($headers)->getJson('/api/plazoPagos')->assertStatus(200);
    }

    // =========================================================
    // Traders (Customers)
    // =========================================================

    public function test_listar_traders()
    {
        $headers = $this->actingAsRole('Traffic');
        $this->withHeaders($headers)->getJson('/api/customers')->assertStatus(200);
    }

    public function test_ver_trader_inexistente()
    {
        $headers = $this->actingAsRole('Traffic');
        $this->withHeaders($headers)->getJson('/api/customer/999999')->assertStatus(404);
    }

    // =========================================================
    // Shippers
    // =========================================================

    public function test_listar_shippers()
    {
        $headers = $this->actingAsRole('Traffic');
        $this->withHeaders($headers)->getJson('/api/customersShipper')->assertStatus(200);
    }

    // =========================================================
    // Consignees
    // =========================================================

    public function test_listar_consignees()
    {
        $headers = $this->actingAsRole('Traffic');
        $this->withHeaders($headers)->getJson('/api/customersCnee')->assertStatus(200);
    }

    // =========================================================
    // Notify
    // =========================================================

    public function test_listar_notifies()
    {
        $headers = $this->actingAsRole('Traffic');
        $this->withHeaders($headers)->getJson('/api/customersNtfy')->assertStatus(200);
    }

    // =========================================================
    // Lugares de Carga / Descarga
    // =========================================================

    public function test_listar_lugares_de_carga()
    {
        $headers = $this->actingAsRole('Traffic');
        $this->withHeaders($headers)->getJson('/api/lugarCargas')->assertStatus(200);
    }

    public function test_listar_lugares_de_descarga()
    {
        $headers = $this->actingAsRole('Traffic');
        $this->withHeaders($headers)->getJson('/api/lugarDescargas')->assertStatus(200);
    }

    // =========================================================
    // Depositos de Retiro
    // =========================================================

    public function test_listar_depositos_retiro()
    {
        $headers = $this->actingAsRole('Traffic');
        $this->withHeaders($headers)->getJson('/api/depositoRetiros')->assertStatus(200);
    }

    // =========================================================
    // Puntos de Interés
    // =========================================================

    public function test_listar_puntos_de_interes()
    {
        $headers = $this->actingAsRole('Traffic');
        $this->withHeaders($headers)->getJson('/api/points_of_interest')->assertStatus(200);
    }

    // =========================================================
    // Empresas
    // =========================================================

    public function test_listar_empresas()
    {
        $headers = $this->actingAsRole('Master');
        $this->withHeaders($headers)->getJson('/api/empresas')->assertStatus(200);
    }

    // =========================================================
    // Configuración PSC
    // =========================================================

    public function test_obtener_configuracion_actual()
    {
        $headers = $this->actingAsRole('Master');
        $response = $this->withHeaders($headers)->getJson('/api/psc/current');
        $response->assertStatus(200);
    }
}
