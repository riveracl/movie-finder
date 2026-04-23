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

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    Http::fake([
        'https://api.themoviedb.org/3/genre/movie/list*' => Http::response([
            'genres' => [
                ['id' => 878, 'name' => 'Science Fiction'],
            ],
        ]),
        'https://api.themoviedb.org/3/movie/popular*' => Http::response([
            'results' => [
                [
                    'id' => 157336,
                    'title' => 'Interstellar',
                    'release_date' => '2014-11-07',
                    'poster_path' => '/poster.jpg',
                    'vote_average' => 8.7,
                    'vote_count' => 2000,
                    'genre_ids' => [878],
                    'overview' => 'Across the stars.',
                ],
            ],
        ]),
        'https://api.themoviedb.org/3/movie/157336*' => Http::response([
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
    ]);

    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('dashboard')
            ->where('search', '')
            ->where('featuredMovie.id', 157336)
            ->has('movies', 1)
            ->where('movies.0.id', 157336)
            ->where('summary.results', 1),
        );
});

test('dashboard search filters movies by title', function () {
    Http::fake([
        'https://api.themoviedb.org/3/genre/movie/list*' => Http::response([
            'genres' => [
                ['id' => 28, 'name' => 'Action'],
            ],
        ]),
        'https://api.themoviedb.org/3/search/movie*' => Http::response([
            'results' => [
                [
                    'id' => 155,
                    'title' => 'The Dark Knight',
                    'release_date' => '2008-07-18',
                    'poster_path' => '/dark-knight.jpg',
                    'vote_average' => 9.0,
                    'vote_count' => 1000,
                    'genre_ids' => [28],
                    'overview' => 'Gotham is at stake.',
                ],
            ],
        ]),
        'https://api.themoviedb.org/3/movie/155*' => Http::response([
            'id' => 155,
            'title' => 'The Dark Knight',
            'release_date' => '2008-07-18',
            'poster_path' => '/dark-knight.jpg',
            'backdrop_path' => '/dark-knight-backdrop.jpg',
            'vote_average' => 9.0,
            'vote_count' => 1000,
            'runtime' => 152,
            'overview' => 'Gotham is at stake.',
            'tagline' => 'Welcome to a world without rules.',
            'original_language' => 'en',
            'genres' => [
                ['name' => 'Action'],
            ],
            'credits' => [
                'cast' => [
                    ['name' => 'Christian Bale'],
                ],
            ],
        ]),
    ]);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard', ['search' => 'dark']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('dashboard')
            ->where('search', 'dark')
            ->where('featuredMovie.id', 155)
            ->has('movies', 1)
            ->where('movies.0.id', 155)
            ->where('summary.results', 1),
        );
});
