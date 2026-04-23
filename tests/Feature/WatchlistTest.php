<?php

use App\Models\Movie;
use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    config()->set('database.default', 'pgsql');
    config()->set('database.connections.pgsql.database', 'laravel');
    DB::beginTransaction();
});

afterEach(function () {
    DB::rollBack();
});

test('authenticated users can add movies to their watchlist', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('watchlist.store'), [
            'tmdb_id' => 157336,
            'title' => 'Interstellar',
            'year' => 2014,
            'poster' => 'https://image.tmdb.org/t/p/w500/poster.jpg',
            'rating' => '8.7',
            'votes' => '2000 votes',
            'primaryGenre' => 'Science Fiction',
            'overview' => 'Across the stars.',
            'href' => '/movies/157336',
        ])
        ->assertRedirect();

    $movie = Movie::query()->where('tmdb_id', 157336)->firstOrFail();

    expect($movie->title)->toBe('Interstellar')
        ->and($user->watchlistMovies()->whereKey($movie->id)->exists())->toBeTrue();
});

test('authenticated users can remove movies from their watchlist', function () {
    $user = User::factory()->create();
    $movie = Movie::factory()->create(['tmdb_id' => 157336, 'title' => 'Interstellar']);
    $user->watchlistMovies()->attach($movie);

    $this->actingAs($user)
        ->delete(route('watchlist.destroy', $movie))
        ->assertRedirect();

    expect($user->watchlistMovies()->whereKey($movie->id)->exists())->toBeFalse();
});

test('watchlist routes require authentication', function () {
    $movie = Movie::factory()->create();

    $this->post(route('watchlist.store'), [
        'tmdb_id' => 1,
        'title' => 'Movie',
    ])->assertRedirect(route('login'));

    $this->delete(route('watchlist.destroy', $movie))
        ->assertRedirect(route('login'));
});
