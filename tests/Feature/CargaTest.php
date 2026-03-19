<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Traits\WithJwtAuth;
use Tests\TestCase;

class CargaTest extends TestCase
{
    use RefreshDatabase, WithJwtAuth;

    // =========================================================
    // GET /api/allCargoThisWeek/{user}
    // =========================================================

    public function test_cargas_semana_actual_requiere_autenticacion()
    {
        $this->getJson('/api/allCargoThisWeek/testuser')->assertStatus(401);
    }

    public function test_cargas_semana_actual_autenticado()
    {
        $user    = User::factory()->create();
        $headers = $this->authHeaders($user);

        $response = $this->withHeaders($headers)->getJson("/api/allCargoThisWeek/{$user->username}");

        $response->assertStatus(200);
    }

    // =========================================================
    // GET /api/allCargoLastWeek/{user}
    // =========================================================

    public function test_cargas_semana_anterior_autenticado()
    {
        $user    = User::factory()->create();
        $headers = $this->authHeaders($user);

        $response = $this->withHeaders($headers)->getJson("/api/allCargoLastWeek/{$user->username}");

        $response->assertStatus(200);
    }

    // =========================================================
    // GET /api/allCargoNextWeek/{user}
    // =========================================================

    public function test_cargas_proxima_semana_autenticado()
    {
        $user    = User::factory()->create();
        $headers = $this->authHeaders($user);

        $response = $this->withHeaders($headers)->getJson("/api/allCargoNextWeek/{$user->username}");

        $response->assertStatus(200);
    }

    // =========================================================
    // GET /api/allCargoFinished/{user}
    // =========================================================

    public function test_cargas_terminadas_autenticado()
    {
        $user    = User::factory()->create();
        $headers = $this->authHeaders($user);

        $response = $this->withHeaders($headers)->getJson("/api/allCargoFinished/{$user->username}");

        $response->assertStatus(200);
    }

    // =========================================================
    // POST /api/issetBooking
    // =========================================================

    public function test_verificar_booking_existente()
    {
        $headers = $this->actingAsRole('Traffic');

        $response = $this->withHeaders($headers)->postJson('/api/issetBooking', [
            'booking' => 'BOOKING-TEST-001',
            'company' => 'empresa_test',
        ]);

        $response->assertStatus(200);
    }

    public function test_verificar_booking_sin_datos_falla()
    {
        $headers = $this->actingAsRole('Traffic');

        $response = $this->withHeaders($headers)->postJson('/api/issetBooking', []);

        $response->assertStatus(422)
                 ->assertJson(['code' => 'VALIDATION_ERROR']);
    }

    // =========================================================
    // GET /api/notifications/*
    // =========================================================

    public function test_notificaciones_requiere_autenticacion()
    {
        $this->getJson('/api/notifications/all')->assertStatus(401);
    }

    public function test_notificaciones_all_autenticado()
    {
        $headers = $this->actingAsRole('Traffic');

        $response = $this->withHeaders($headers)->getJson('/api/notifications/all');

        $response->assertStatus(200);
    }

    public function test_notificaciones_problems_autenticado()
    {
        $headers = $this->actingAsRole('Traffic');

        $response = $this->withHeaders($headers)->getJson('/api/notifications/problems');

        $response->assertStatus(200);
    }

    public function test_notificaciones_completed_autenticado()
    {
        $headers = $this->actingAsRole('Traffic');

        $response = $this->withHeaders($headers)->getJson('/api/notifications/completed');

        $response->assertStatus(200);
    }

    // =========================================================
    // GET /api/messages/unread
    // =========================================================

    public function test_mensajes_no_leidos_autenticado()
    {
        $headers = $this->actingAsRole('Traffic');

        $response = $this->withHeaders($headers)->getJson('/api/messages/unread');

        $response->assertStatus(200);
    }

    // =========================================================
    // GET /api/cargasActivas
    // =========================================================

    public function test_cargas_activas_autenticado()
    {
        $headers = $this->actingAsRole('Traffic');

        $response = $this->withHeaders($headers)->getJson('/api/cargasActivas');

        $response->assertStatus(200);
    }
}
