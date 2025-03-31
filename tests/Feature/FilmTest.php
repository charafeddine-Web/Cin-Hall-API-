<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Film;
use Tymon\JWTAuth\Facades\JWTAuth;

class FilmTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_all_films()
    {
        $user = User::factory()->create();
        $user->role = 'admin';
        $user->save();

        Film::factory(3)->create();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/films');

        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => [
                    'id', 'titre', 'description', 'image', 'duree',
                ]
            ]);
    }

    public function test_get_film_by_id()
    {
        $user = User::factory()->create();
        $user->role = 'admin';
        $user->save();

        $film = Film::factory()->create();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get("/api/films/{$film->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $film->id,
                'titre' => $film->titre,
                'description' => $film->description,
            ]);
    }

    public function test_create_film()
    {
        $user = User::factory()->create();
        $user->role = 'admin';
        $user->save();

        $token = JWTAuth::fromUser($user);

        $data = [
            'titre' => 'New Film',
            'description' => 'A new film description',
            'image' => 'http://example.com/image.jpg',
            'duree' => 120,
            'age_minimum' => 18,
            'bande_annonce' => 'http://youtube.com/film_trailer',
            'genre' => 'Action',
            'acteurs' => json_encode(['Actor 1', 'Actor 2']),
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/films', $data);

        $response->assertStatus(201)
            ->assertJson([
                'titre' => 'New Film',
                'description' => 'A new film description',
                'image' => 'http://example.com/image.jpg',
                'duree' => 120,
                'age_minimum' => 18,
                'genre' => 'Action',
                'acteurs' => '["Actor 1", "Actor 2"]',
            ]);
    }

    public function test_update_film()
    {
        $user = User::factory()->create();
        $user->save();

        $token = JWTAuth::fromUser($user);

        $film = Film::factory()->create();

        $updatedData = [
            'titre' => 'Updated Film Title',
            'description' => 'Updated description',
            'image' => 'http://example.com/updated-image.jpg',
            'duree' => 130,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/films/{$film->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson($updatedData);

        $this->assertDatabaseHas('films', $updatedData);
    }
}
