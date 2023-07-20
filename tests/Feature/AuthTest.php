<?php

namespace Tests\Feature;

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_fails_with_admin_role(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => 'validPassword',
            'password_confirmation' => 'validPassword',
            'role_id' => Role::ROLE_ADMINISTRATOR
        ]);

        $response->assertStatus(422);
    }

    public function test_registration_succeeds_with_user_role(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => 'validPassword',
            'password_confirmation' => 'validPassword',
            'role_id' => Role::ROLE_USER
        ]);

        $response->assertStatus(201)->assertJsonStructure([
            'access_token',
        ]);
    }

    public function test_registration_succeeds_with_owner_role(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => 'validPassword',
            'password_confirmation' => 'validPassword',
            'role_id' => Role::ROLE_OWNER
        ]);

        $response->assertStatus(201)->assertJsonStructure([
            'access_token',
        ]);
    }
}
