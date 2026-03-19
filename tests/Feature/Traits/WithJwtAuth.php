<?php

namespace Tests\Feature\Traits;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Tymon\JWTAuth\Facades\JWTAuth;

trait WithJwtAuth
{
    protected function createUserWithRole(string $role, array $attributes = []): User
    {
        $user = User::factory()->create($attributes);

        if (!Role::where('name', $role)->exists()) {
            Role::create(['name' => $role, 'guard_name' => 'api']);
        }

        $user->assignRole($role);

        return $user;
    }

    protected function authHeaders(User $user): array
    {
        $token = JWTAuth::fromUser($user);

        return [
            'Authorization' => "Bearer $token",
            'Accept'        => 'application/json',
        ];
    }

    protected function actingAsRole(string $role, array $attributes = []): array
    {
        $user = $this->createUserWithRole($role, $attributes);
        return $this->authHeaders($user);
    }
}
