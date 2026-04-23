<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class TmdbMovieService
{
    /**
     * @return array{
     *     movies: list<array{
     *         id: int,
     *         title: string,
     *         year: int|null,
     *         poster: string,
     *         rating: string,
     *         votes: string,
     *         primaryGenre: string,
     *         overview: string,
     *         href: string
     *     }>,
     *     summary: array{
     *         results: int,
     *         audienceVotes: string,
     *         curatedLists: int,
     *         sourceLabel: string
     *     }
     * }
     */
    public function discoverMovies(?string $search = null): array
    {
        $movies = $search !== null
            ? $this->searchMoviesByTitle($search)
            : $this->popularMovies();

        return [
            'movies' => $movies,
            'summary' => [
                'results' => count($movies),
                'audienceVotes' => $this->formatAudienceVotes($movies),
                'curatedLists' => 12,
                'sourceLabel' => $search !== null ? 'TMDB search results' : 'Popular on TMDB',
            ],
        ];
    }

    /**
     * @return array{
     *     id: int,
     *     title: string,
     *     year: int|null,
     *     rating: string,
     *     votes: string,
     *     runtime: string,
     *     releaseDate: string,
     *     poster: string,
     *     backdrop: string,
     *     href: string,
     *     genres: list<string>,
     *     cast: list<string>,
     *     overview: string,
     *     tagline: string,
     *     language: string
     * }|null
     */
    public function getMovieDetails(int $movieId): ?array
    {
        $response = $this->request(
            "movie/{$movieId}",
            ['append_to_response' => 'credits'],
            allowNotFound: true,
        );

        if ($response->status() === 404) {
            return null;
        }

        /** @var array<string, mixed> $movie */
        $movie = $response->json();

        return $this->mapMovieDetails($movie);
    }

    /**
     * @return list<array{
     *     id: int,
     *     title: string,
     *     year: int|null,
     *     poster: string,
     *     rating: string,
     *     votes: string,
     *     primaryGenre: string,
     *     overview: string,
     *     href: string
     * }>
     */
    public function recommendedMovies(int $movieId, int $limit = 3): array
    {
        $genreNames = $this->genreNames();
        /** @var list<array<string, mixed>> $results */
        $results = $this->request(
            "movie/{$movieId}/recommendations",
            ['page' => 1],
            allowNotFound: true,
        )->json('results', []);

        return collect($results)
            ->take($limit)
            ->map(fn (array $movie): array => $this->mapMovieCard($movie, $genreNames))
            ->filter(fn (array $movie): bool => $movie['poster'] !== '')
            ->values()
            ->all();
    }

    /**
     * @return list<array{
     *     id: int,
     *     title: string,
     *     year: int|null,
     *     poster: string,
     *     rating: string,
     *     votes: string,
     *     primaryGenre: string,
     *     overview: string,
     *     href: string
     * }>
     */
    private function popularMovies(): array
    {
        $genreNames = $this->genreNames();
        /** @var list<array<string, mixed>> $results */
        $results = $this->request('movie/popular', ['page' => 1])->json('results', []);

        return collect($results)
            ->take(12)
            ->map(fn (array $movie): array => $this->mapMovieCard($movie, $genreNames))
            ->filter(fn (array $movie): bool => $movie['poster'] !== '')
            ->values()
            ->all();
    }

    /**
     * @return list<array{
     *     id: int,
     *     title: string,
     *     year: int|null,
     *     poster: string,
     *     rating: string,
     *     votes: string,
     *     primaryGenre: string,
     *     overview: string,
     *     href: string
     * }>
     */
    private function searchMoviesByTitle(string $search): array
    {
        $genreNames = $this->genreNames();
        /** @var list<array<string, mixed>> $results */
        $results = $this->request('search/movie', [
            'query' => $search,
            'include_adult' => 'false',
            'page' => 1,
        ])->json('results', []);

        return collect($results)
            ->take(12)
            ->map(fn (array $movie): array => $this->mapMovieCard($movie, $genreNames))
            ->filter(fn (array $movie): bool => $movie['poster'] !== '')
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function genreNames(): array
    {
        /** @var list<array{id:int, name:string}> $genres */
        $genres = $this->request('genre/movie/list')->json('genres', []);

        return collect($genres)
            ->mapWithKeys(fn (array $genre): array => [$genre['id'] => $genre['name']])
            ->all();
    }

    /**
     * @param  array<string, mixed>  $movie
     * @param  array<int, string>  $genreNames
     * @return array{
     *     id: int,
     *     title: string,
     *     year: int|null,
     *     poster: string,
     *     rating: string,
     *     votes: string,
     *     primaryGenre: string,
     *     overview: string,
     *     href: string
     * }
     */
    private function mapMovieCard(array $movie, array $genreNames): array
    {
        $movieId = (int) Arr::get($movie, 'id');

        return [
            'id' => $movieId,
            'title' => (string) Arr::get($movie, 'title', 'Untitled'),
            'year' => $this->extractYear(Arr::get($movie, 'release_date')),
            'poster' => $this->imageUrl(Arr::get($movie, 'poster_path'), 'w500'),
            'rating' => number_format((float) Arr::get($movie, 'vote_average', 0), 1),
            'votes' => number_format((int) Arr::get($movie, 'vote_count', 0)).' votes',
            'primaryGenre' => $this->primaryGenre(Arr::get($movie, 'genre_ids', []), $genreNames),
            'overview' => (string) Arr::get($movie, 'overview', 'No overview available yet.'),
            'href' => route('movies.show', $movieId),
        ];
    }

    /**
     * @param  array<string, mixed>  $movie
     * @return array{
     *     id: int,
     *     title: string,
     *     year: int|null,
     *     rating: string,
     *     votes: string,
     *     runtime: string,
     *     releaseDate: string,
     *     poster: string,
     *     backdrop: string,
     *     href: string,
     *     genres: list<string>,
     *     cast: list<string>,
     *     overview: string,
     *     tagline: string,
     *     language: string
     * }
     */
    private function mapMovieDetails(array $movie): array
    {
        $movieId = (int) Arr::get($movie, 'id');
        /** @var list<array{name:string}> $genres */
        $genres = Arr::get($movie, 'genres', []);
        /** @var list<array{name:string}> $cast */
        $cast = Arr::get($movie, 'credits.cast', []);
        $runtime = (int) Arr::get($movie, 'runtime', 0);

        return [
            'id' => $movieId,
            'title' => (string) Arr::get($movie, 'title', 'Untitled'),
            'year' => $this->extractYear(Arr::get($movie, 'release_date')),
            'rating' => number_format((float) Arr::get($movie, 'vote_average', 0), 1),
            'votes' => number_format((int) Arr::get($movie, 'vote_count', 0)),
            'runtime' => $runtime > 0 ? "{$runtime} min" : 'Unknown',
            'releaseDate' => (string) Arr::get($movie, 'release_date', 'Unknown'),
            'poster' => $this->imageUrl(Arr::get($movie, 'poster_path'), 'w500'),
            'backdrop' => $this->imageUrl(Arr::get($movie, 'backdrop_path'), 'w1280'),
            'href' => route('movies.show', $movieId),
            'genres' => collect($genres)->pluck('name')->take(4)->values()->all(),
            'cast' => collect($cast)->pluck('name')->take(6)->values()->all(),
            'overview' => (string) Arr::get($movie, 'overview', 'No overview available yet.'),
            'tagline' => (string) Arr::get($movie, 'tagline', 'Discover the story behind the title.'),
            'language' => strtoupper((string) Arr::get($movie, 'original_language', 'n/a')),
        ];
    }

    /**
     * @param  array<int, mixed>  $genreIds
     * @param  array<int, string>  $genreNames
     */
    private function primaryGenre(array $genreIds, array $genreNames): string
    {
        foreach ($genreIds as $genreId) {
            $name = $genreNames[(int) $genreId] ?? null;

            if ($name !== null) {
                return $name;
            }
        }

        return 'Movie';
    }

    /**
     * @param  list<array{id:int, title:string, year:int|null, poster:string, rating:string, votes:string, primaryGenre:string, overview:string, href:string}>  $movies
     */
    private function formatAudienceVotes(array $movies): string
    {
        if ($movies === []) {
            return '0 votes';
        }

        $totalVotes = collect($movies)
            ->sum(fn (array $movie): int => (int) str_replace([',', ' votes'], '', $movie['votes']));

        return number_format($totalVotes).' total';
    }

    private function extractYear(mixed $releaseDate): ?int
    {
        if (! is_string($releaseDate) || $releaseDate === '') {
            return null;
        }

        return (int) substr($releaseDate, 0, 4);
    }

    private function imageUrl(mixed $path, string $size): string
    {
        if (! is_string($path) || $path === '') {
            return '';
        }

        $imageUrl = trim((string) config('services.tmdb.image_url', 'https://image.tmdb.org/t/p'), '/');

        return "{$imageUrl}/{$size}{$path}";
    }

    private function request(
        string $endpoint,
        array $query = [],
        bool $allowNotFound = false,
    ): Response {
        $token = trim((string) config('services.tmdb.access_token'));
        $apiKey = trim((string) config('services.tmdb.api_key'));
        $baseUrl = trim((string) config('services.tmdb.base_url', 'https://api.themoviedb.org/3'));
        $verifySsl = (bool) config('services.tmdb.verify_ssl', true);

        if ($token === '' && $apiKey === '') {
            throw TmdbServiceException::missingCredentials();
        }

        $request = Http::baseUrl($baseUrl)
            ->acceptJson()
            ->timeout(10);

        if (! $verifySsl) {
            $request = $request->withoutVerifying();
        }

        if ($token !== '') {
            $request = $request->withToken($token);
        } else {
            $query['api_key'] = $apiKey;
        }

        $response = $request->get($endpoint, [
            'language' => 'en-US',
            ...$query,
        ]);

        if ($allowNotFound && $response->status() === 404) {
            return $response;
        }

        if ($response->successful()) {
            return $response;
        }

        $message = (string) $response->json('status_message', 'TMDB request failed.');

        throw TmdbServiceException::requestFailed($message, $response->status());
    }
}
