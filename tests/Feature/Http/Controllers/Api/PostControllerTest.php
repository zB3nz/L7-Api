<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_store()
    {
        $this->withoutExceptionHandling();

        $response = $this->json('POST', '/api/posts', [
            'title' => 'Post de prueba'
        ]);

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at']) //confirma estructura
            ->assertJson(['title' => 'Post de prueba']) //confirma que existe los datos
            ->assertStatus(201); //recurso creado

        $this->assertDatabaseHas('posts', ['title' => 'Post de prueba']); //que existe en la base de datos
    }
}
