<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    config()->set('database.default', 'pgsql');
    config()->set('database.connections.pgsql.database', 'laravel');
    DB::beginTransaction();
});

afterEach(function () {
    DB::rollBack();
});

test('guests are redirected away from movie details', function () {
    $this->get(route('movies.show', 157336))
        ->assertRedirect(route('login'));
});

test('authenticated users can view a movie detail page', function () {
    Http::fake([
        'https://api.themoviedb.org/3/movie/157336' => Http::response([
            'id' => 157336,
            'title' => 'Interstellar',
            'release_date' => '2014-11-07',
            'poster_path' => '/poster.jpg',
            'backdrop_path' => '/backdrop.jpg',
            'vote_average' => 8.7,
            'vote_count' => 2000,
            'runtime' => 169,
            'overview' => 'Across the stars.',
            'tagline' => 'Go further.',
            'original_language' => 'en',
            'genres' => [
                ['name' => 'Science Fiction'],
            ],
            'credits' => [
                'cast' => [
                    ['name' => 'Matthew McConaughey'],
                ],
            ],
        ]),
        'https://api.themoviedb.org/3/movie/157336?*' => Http::response([
            'id' => 157336,
            'title' => 'Interstellar',
            'release_date' => '2014-11-07',
            'poster_path' => '/poster.jpg',
            'backdrop_path' => '/backdrop.jpg',
            'vote_average' => 8.7,
            'vote_count' => 2000,
            'runtime' => 169,
            'overview' => 'Across the stars.',
            'tagline' => 'Go further.',
            'original_language' => 'en',
            'genres' => [
                ['name' => 'Science Fiction'],
            ],
            'credits' => [
                'cast' => [
                    ['name' => 'Matthew McConaughey'],
                ],
            ],
        ]),
        'https://api.themoviedb.org/3/movie/157336/recommendations*' => Http::response([
            'results' => [
                [
                    'id' => 11,
                    'title' => 'Recommended Movie',
                    'release_date' => '2020-01-01',
                    'poster_path' => '/recommended.jpg',
                    'vote_average' => 7.1,
                    'vote_count' => 321,
                    'genre_ids' => [878],
                    'overview' => 'Another space journey.',
                ],
            ],
        ]),
        'https://api.themoviedb.org/3/genre/movie/list*' => Http::response([
            'genres' => [
                ['id' => 878, 'name' => 'Science Fiction'],
            ],
        ]),
    ]);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('movies.show', 157336))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('movies/show')
            ->where('movie.id', 157336)
            ->where('movie.title', 'Interstellar')
            ->where('movie.runtime', '169 min')
            ->where('movie.releaseDate', '2014-11-07')
            ->where('movie.genres.0', 'Science Fiction')
            ->where('movie.cast.0', 'Matthew McConaughey')
            ->has('relatedMovies', 1),
        );
});

test('unknown movie detail pages return a 404 response', function () {
    Http::fake([
        'https://api.themoviedb.org/3/movie/999999*' => Http::response([
            'status_message' => 'The resource you requested could not be found.',
        ], 404),
    ]);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('movies.show', 999999))
        ->assertNotFound();
});
