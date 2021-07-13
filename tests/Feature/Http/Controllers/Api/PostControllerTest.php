<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Post;
use App\User;

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

        //genera el usuario
        $user = factory(User::class)->create();

        //realiza la peticion
        $response = $this->actingAs($user, 'api')->json('POST', '/api/posts', [
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

        //genera el usuario
        $user = factory(User::class)->create();

        //realiza la peticion
        $response = $this->actingAs($user, 'api')->json('POST', '/api/posts', [
            'title' => ''
        ]);

        $response->assertStatus(422) //http 422, incompletada
            ->assertJsonValidationErrors('title'); 
    }

    public function test_show()
    {
        //$this->withoutExceptionHandling();

        //genera el usuario
        $user = factory(User::class)->create();

        //crea y guarda los datos
        $post = factory(Post::class)->create();

        $response = $this->actingAs($user, 'api')->json('GET', "/api/posts/$post->id");

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at']) //confirma estructura
        ->assertJson(['title' => $post->title]) //confirma que existe los datos
        ->assertStatus(200); //acceso ok
    }

    public function test_404_show()
    {
        //genera el usuario
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->json('GET', "/api/posts/5");

        $response->assertStatus(404); //acceso ok
    }

    public function test_update()
    {
        //$this->withoutExceptionHandling();

        //genera el usuario
        $user = factory(User::class)->create();

        //crea y guarda los datos
        $post = factory(Post::class)->create();

        $response = $this->actingAs($user, 'api')->json('PUT', "/api/posts/$post->id", ['title' => 'nuevo']);

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at']) //confirma estructura
            ->assertJson(['title' => 'nuevo']) //confirma que existe los datos
            ->assertStatus(200); //ok

        $this->assertDatabaseHas('posts', ['title' => 'nuevo']); //que existe en la base de datos
    }

    public function test_delete()
    {
        //$this->withoutExceptionHandling();

        //genera el usuario
        $user = factory(User::class)->create();

        //crea y guarda los datos
        $post = factory(Post::class)->create();

        $response = $this->actingAs($user, 'api')->json('DELETE', "/api/posts/$post->id");

        $response->assertSee(null) //confirma que existe los datos
            ->assertStatus(204); //sin contenido

        $this->assertDatabaseMissing('posts', ['id' => $post->id]); //que existe en la base de datos
    }

    public function test_index()
    {
        //$this->withoutExceptionHandling();

        //genera el usuario
        $user = factory(User::class)->create();

        //crea y guarda los datos
        $post = factory(Post::class, 5)->create();

        //realiza la consulta
        $response = $this->actingAs($user, 'api')->json('GET', "/api/posts");
        
        //debe contener muchos datos con estos campos
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'created_at', 'updated_at'] 
            ]
        ])->assertStatus(200); //ok
    }

    public function test_guest()
    {
        $this->json('GET',    '/api/posts')->assertStatus(401); //no autorizados
        $this->json('POST',   '/api/posts')->assertStatus(401); //no autorizados
        $this->json('GET',    '/api/posts/1000')->assertStatus(401); //no autorizados
        $this->json('PUT',    '/api/posts/1000')->assertStatus(401); //no autorizados
        $this->json('DELETE', '/api/posts/1000')->assertStatus(401); //no autorizados
    }
}
