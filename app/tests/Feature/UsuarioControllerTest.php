<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UsuarioControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->initDatabase();
    }

    public function tearDown(): void
    {
        $this->resetDatabase();
        parent::tearDown();
    }

    public function test_usuario_nuevo_incorrecto()
    {
        $response = $this->post('/api/usuario', [
            //
        ], [
            'accept' => 'application/json'
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_usuario_nuevo_duplicado()
    {
        $response = $this->post('/api/usuario', [
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => 'admin',
            'password_confirmation' => 'admin'
        ], [
            'accept' => 'application/json'
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_usuario_nuevo_correcto()
    {
        $response = $this->post('/api/usuario', [
            'name' => 'test1',
            'email' => 'test@test.com',
            'password' => 'test1',
            'password_confirmation' => 'test1'
        ], [
            'accept' => 'application/json'
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }
}
