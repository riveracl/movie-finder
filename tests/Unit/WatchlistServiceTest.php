<?php

use App\Models\Movie;
use App\Models\User;
use App\Services\WatchlistService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    config()->set('database.default', 'pgsql');
    config()->set('database.connections.pgsql.database', 'laravel');
    DB::beginTransaction();
});

afterEach(function () {
    DB::rollBack();
});

test('it decorates movie cards with watchlist metadata', function () {
    $user = User::factory()->create();
    $savedMovie = Movie::factory()->create([
        'tmdb_id' => 157336,
        'title' => 'Interstellar',
    ]);
    $user->watchlistMovies()->attach($savedMovie);

    $service = app(WatchlistService::class);
    $decorated = $service->decorateMovieCards([
        [
            'id' => 157336,
            'title' => 'Interstellar',
            'year' => 2014,
            'poster' => 'https://image.tmdb.org/t/p/w500/poster.jpg',
            'rating' => '8.7',
            'votes' => '2000 votes',
            'primaryGenre' => 'Science Fiction',
            'overview' => 'Across the stars.',
            'href' => '/movies/157336',
        ],
    ], $service->idsByTmdbIdFor($user));

    expect($decorated[0]['isWatchlisted'])->toBeTrue()
        ->and($decorated[0]['watchlistId'])->toBe($savedMovie->id)
        ->and($decorated[0]['tmdbId'])->toBe(157336);
});

test('it stores movies and attaches them to a users watchlist', function () {
    $user = User::factory()->create();
    $service = app(WatchlistService::class);

    $movie = $service->add($user, [
        'tmdb_id' => 550,
        'title' => 'Fight Club',
        'year' => 1999,
        'poster' => 'https://image.tmdb.org/t/p/w500/poster.jpg',
        'rating' => '8.4',
        'votes' => '1500 votes',
        'primaryGenre' => 'Drama',
        'overview' => 'An insomniac office worker crosses paths with a soap salesman.',
        'href' => '/movies/550',
    ]);

    expect($movie->tmdb_id)->toBe(550)
        ->and($user->watchlistMovies()->whereKey($movie->id)->exists())->toBeTrue();
});
