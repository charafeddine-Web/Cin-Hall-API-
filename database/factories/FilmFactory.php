<?php

namespace Database\Factories;

use App\Models\Film;
use Illuminate\Database\Eloquent\Factories\Factory;

class FilmFactory extends Factory
{
    protected $model = Film::class;

    public function definition()
    {
        return [
            'titre' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'image' => $this->faker->imageUrl(),
            'duree' => $this->faker->numberBetween(80, 180), // duration in minutes
            'age_minimum' => $this->faker->numberBetween(0, 18), // age rating
            'bande_annonce' => $this->faker->url, // URL for the trailer
            'genre' => $this->faker->word, // genre of the movie
            'acteurs' => json_encode($this->faker->words(3)), // actors as a JSON array
        ];
    }
}
