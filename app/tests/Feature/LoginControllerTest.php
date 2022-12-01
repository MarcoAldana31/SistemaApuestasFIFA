<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginControllerTest extends TestCase
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

    public function test_login_post_validate_data()
    {
        $response = $this->post('/api/login', [
            //
        ], [
            'accept' => 'application/json'
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_login_post_validate_data_usuario_incorrecto()
    {
        $response = $this->post('/api/login', [
            'username' => 'demo',
            'password' => 'demo'
        ], [
            'accept' => 'application/json'
        ]);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_login_post_validate_data_usuario_correcto()
    {
        $response = $this->post('/api/login', [
            'username' => 'admin',
            'password' => 'admin2022'
        ], [
            'accept' => 'application/json'
        ]);

        $response->assertStatus(Response::HTTP_OK);
    }
}
