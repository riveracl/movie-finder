<?php

namespace Database\Factories;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Movie>
 */
class MovieFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tmdb_id' => fake()->unique()->numberBetween(1_000, 999_999),
            'title' => fake()->sentence(3),
            'year' => fake()->numberBetween(1980, 2025),
            'poster' => 'https://image.tmdb.org/t/p/w500/poster.jpg',
            'rating' => number_format(fake()->randomFloat(1, 6, 9), 1),
            'votes' => fake()->numberBetween(100, 20_000).' votes',
            'primary_genre' => fake()->randomElement(['Drama', 'Action', 'Comedy']),
            'overview' => fake()->paragraph(),
            'href' => '/movies/'.fake()->numberBetween(1_000, 999_999),
        ];
    }
}
