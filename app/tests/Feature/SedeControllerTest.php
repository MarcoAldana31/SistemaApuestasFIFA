<?php

namespace Tests\Feature;

use App\Models\Sede;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class SedeControllerTest extends TestCase
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

    public function test_consultar_sedes()
    {
        $response = $this->get('/api/sede');

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_consultar_sedes_filtro()
    {
        Sede::create([
            'nombre' => 'Central',
            'pais' => 'Guatemala',
            'estado' => 1,
        ]);

        $response = $this->get('/api/sede?nombre=Ce');

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_validar_registro_sede()
    {
        $response = $this->post('/api/sede', [
            //
        ], [
            'accept' => 'application/json'
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_validar_registro_sede_incorrecto_limite_nombre()
    {
        $response = $this->post('/api/sede', [
            'nombre' => rand(51, 100)
        ], [
            'accept' => 'application/json'
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_validar_registro_sede_correcto()
    {
        $response = $this->post('/api/sede', [
            'nombre' => 'Central',
            'pais' => 'Guatemala'
        ], [
            'accept' => 'application/json'
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function test_actualizar_sede_no_existe()
    {
        $response = $this->put('/api/sede/1000000', [
            //
        ], [
            'accept' => 'application/json'
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_actualizar_sede_correcto()
    {
        $sede = Sede::create([
            'nombre' => 'Central',
            'pais' => 'Guatemala',
            'estado' => 1,
        ]);

        $response = $this->put('/api/sede/' . $sede->id, [
            'nombre' => 'Central',
            'pais' => 'Guatemala'
        ], [
            'accept' => 'application/json'
        ]);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_eliminar_sede_correcto()
    {
        $sede = Sede::create([
            'nombre' => 'Central',
            'pais' => 'Guatemala',
            'estado' => 1,
        ]);

        $response = $this->delete('/api/sede/' . $sede->id, [
            //
        ], [
            'accept' => 'application/json'
        ]);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }


}
