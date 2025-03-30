<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class FilmsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('films')->insert([
            [
                'titre' => 'Le Seigneur des Anneaux : La Communauté de l\'Anneau',
                'description' => 'Un film de fantasy réalisé par Peter Jackson, basé sur le roman de J.R.R. Tolkien.',
                'image' => 'https://example.com/lotr.jpg',
                'duree' => 178,
                'age_minimum' => 12,
                'bande_annonce' => 'https://www.youtube.com/watch?v=Pki6jbSbXIY',
                'genre' => 'Fantasy, Aventure',
                'acteurs' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titre' => 'Inception',
                'description' => 'Un thriller de science-fiction réalisé par Christopher Nolan, avec Leonardo DiCaprio.',
                'image' => 'https://example.com/inception.jpg',
                'duree' => 148,
                'age_minimum' => 13,
                'bande_annonce' => 'https://www.youtube.com/watch?v=YoHD9XEInc0',
                'genre' => 'Science-fiction, Thriller',
                'acteurs' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titre' => 'The Dark Knight',
                'description' => 'Un film de super-héros réalisé par Christopher Nolan, avec Christian Bale et Heath Ledger.',
                'image' => 'https://example.com/darkknight.jpg',
                'duree' => 152,
                'age_minimum' => 12,
                'bande_annonce' => 'https://www.youtube.com/watch?v=EXeTwQWrcwY',
                'genre' => 'Action, Crime, Drame',
                'acteurs' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titre' => 'Avatar',
                'description' => 'Un film de science-fiction réalisé par James Cameron, avec Sam Worthington et Zoe Saldana.',
                'image' => 'https://example.com/avatar.jpg',
                'duree' => 162,
                'age_minimum' => 10,
                'bande_annonce' => 'https://www.youtube.com/watch?v=5PSNL1qE6VY',
                'genre' => 'Science-fiction, Aventure',
                'acteurs' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titre' => 'Pulp Fiction',
                'description' => 'Un film culte réalisé par Quentin Tarantino, avec John Travolta, Uma Thurman et Samuel L. Jackson.',
                'image' => 'https://example.com/pulpfiction.jpg',
                'duree' => 154,
                'age_minimum' => 16,
                'bande_annonce' => 'https://www.youtube.com/watch?v=s7EdQ4FqbhY',
                'genre' => 'Crime, Drame',
                'acteurs' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

    }
}
