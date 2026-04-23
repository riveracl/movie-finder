<?php

use App\Services\TmdbMovieService;
use App\Services\TmdbServiceException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

uses(TestCase::class);

test('it searches tmdb movies and maps movie cards', function () {
    config()->set('services.tmdb.access_token', 'test-token');
    config()->set('services.tmdb.api_key', null);
    config()->set('services.tmdb.base_url', 'https://api.themoviedb.org/3');
    config()->set('services.tmdb.verify_ssl', true);

    Http::fake([
        'https://api.themoviedb.org/3/genre/movie/list*' => Http::response([
            'genres' => [
                ['id' => 878, 'name' => 'Science Fiction'],
            ],
        ]),
        'https://api.themoviedb.org/3/search/movie*' => Http::response([
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
    ]);

    $result = app(TmdbMovieService::class)->discoverMovies('Interstellar');

    expect($result['movies'])->toHaveCount(1)
        ->and($result['movies'][0]['id'])->toBe(157336)
        ->and($result['movies'][0]['primaryGenre'])->toBe('Science Fiction')
        ->and($result['summary']['sourceLabel'])->toBe('TMDB search results');
});

test('it returns null when a movie detail request is not found', function () {
    config()->set('services.tmdb.access_token', 'test-token');
    config()->set('services.tmdb.api_key', null);
    config()->set('services.tmdb.verify_ssl', true);

    Http::fake([
        'https://api.themoviedb.org/3/movie/404*' => Http::response([
            'status_message' => 'The resource you requested could not be found.',
        ], 404),
    ]);

    expect(app(TmdbMovieService::class)->getMovieDetails(404))->toBeNull();
});

test('it throws a clean exception when tmdb credentials are missing', function () {
    config()->set('services.tmdb.access_token', null);
    config()->set('services.tmdb.api_key', null);
    config()->set('services.tmdb.verify_ssl', true);

    expect(fn () => app(TmdbMovieService::class)->discoverMovies())
        ->toThrow(TmdbServiceException::class);
});

test('it can query tmdb when ssl verification is disabled for this service', function () {
    config()->set('services.tmdb.access_token', 'test-token');
    config()->set('services.tmdb.api_key', null);
    config()->set('services.tmdb.base_url', 'https://api.themoviedb.org/3');
    config()->set('services.tmdb.verify_ssl', false);

    Http::fake([
        'https://api.themoviedb.org/3/genre/movie/list*' => Http::response([
            'genres' => [
                ['id' => 878, 'name' => 'Science Fiction'],
            ],
        ]),
        'https://api.themoviedb.org/3/search/movie*' => Http::response([
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
    ]);

    $result = app(TmdbMovieService::class)->discoverMovies('Interstellar');

    expect($result['movies'])->toHaveCount(1)
        ->and($result['movies'][0]['title'])->toBe('Interstellar');
});
