<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Post;

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
        //$this->withoutExceptionHandling();

        $response = $this->json('POST', '/api/posts', [
            'title' => 'Post de prueba'
        ]);

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at']) //confirma estructura
            ->assertJson(['title' => 'Post de prueba']) //confirma que existe los datos
            ->assertStatus(201); //recurso creado

        $this->assertDatabaseHas('posts', ['title' => 'Post de prueba']); //que existe en la base de datos
    }

    public function test_validate_title()
    {
        //$this->withoutExceptionHandling();

        $response = $this->json('POST', '/api/posts', [
            'title' => ''
        ]);

        $response->assertStatus(422) //http 422, incompletada
            ->assertJsonValidationErrors('title'); 
    }

    public function test_show()
    {
        $post = factory(Post::class)-> create();

        $response = $this->json('GET', "/api/posts/$post->id");

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at']) //confirma estructura
        ->assertJson(['title' => $post->title]) //confirma que existe los datos
        ->assertStatus(200); //acceso ok
    }

    public function test_404_show()
    {
        $response = $this->json('GET', "/api/posts/5");

        $response->assertStatus(404); //acceso ok
    }
}
