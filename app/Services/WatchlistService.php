<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class WatchlistService
{
    /**
     * @param  array{
     *     tmdb_id: int,
     *     title: string,
     *     year?: int|null,
     *     poster?: string|null,
     *     rating?: string|null,
     *     votes?: string|null,
     *     primaryGenre?: string|null,
     *     overview?: string|null,
     *     href?: string|null
     * }  $movieData
     */
    public function add(User $user, array $movieData): Movie
    {
        $movie = Movie::query()->updateOrCreate(
            ['tmdb_id' => $movieData['tmdb_id']],
            [
                'title' => $movieData['title'],
                'year' => $movieData['year'] ?? null,
                'poster' => $movieData['poster'] ?? null,
                'rating' => $movieData['rating'] ?? null,
                'votes' => $movieData['votes'] ?? null,
                'primary_genre' => $movieData['primaryGenre'] ?? null,
                'overview' => $movieData['overview'] ?? null,
                'href' => $movieData['href'] ?? null,
            ],
        );

        $user->watchlistMovies()->syncWithoutDetaching([$movie->id]);

        return $movie;
    }

    public function remove(User $user, Movie $movie): void
    {
        $user->watchlistMovies()->detach($movie);
    }

    /**
     * @return list<array{
     *     id: int,
     *     tmdbId: int,
     *     title: string,
     *     year: int|null,
     *     poster: string,
     *     rating: string,
     *     votes: string,
     *     primaryGenre: string,
     *     overview: string,
     *     href: string,
     *     isWatchlisted: true,
     *     watchlistId: int
     * }>
     */
    public function itemsFor(User $user): array
    {
        return $this->mapWatchlistMovies(
            $user->watchlistMovies()
                ->orderByDesc('movie_user.created_at')
                ->get(),
        );
    }

    /**
     * @return array<int, int>
     */
    public function idsByTmdbIdFor(User $user): array
    {
        return $user->watchlistMovies()
            ->get(['movies.id', 'movies.tmdb_id'])
            ->mapWithKeys(fn (Movie $movie): array => [$movie->tmdb_id => $movie->id])
            ->all();
    }

    /**
     * @param  list<array{
     *     id: int,
     *     title: string,
     *     year: int|null,
     *     poster: string,
     *     rating: string,
     *     votes: string,
     *     primaryGenre: string,
     *     overview: string,
     *     href: string
     * }>  $movies
     * @param  array<int, int>  $watchlistIdsByTmdbId
     * @return list<array{
     *     id: int,
     *     tmdbId: int,
     *     title: string,
     *     year: int|null,
     *     poster: string,
     *     rating: string,
     *     votes: string,
     *     primaryGenre: string,
     *     overview: string,
     *     href: string,
     *     isWatchlisted: bool,
     *     watchlistId: int|null
     * }>
     */
    public function decorateMovieCards(array $movies, array $watchlistIdsByTmdbId): array
    {
        return array_map(function (array $movie) use ($watchlistIdsByTmdbId): array {
            $watchlistId = $watchlistIdsByTmdbId[$movie['id']] ?? null;

            return [
                ...$movie,
                'tmdbId' => $movie['id'],
                'isWatchlisted' => $watchlistId !== null,
                'watchlistId' => $watchlistId,
            ];
        }, $movies);
    }

    /**
     * @param  array{
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
     * }  $movie
     * @param  array<int, int>  $watchlistIdsByTmdbId
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
     *     language: string,
     *     tmdbId: int,
     *     isWatchlisted: bool,
     *     watchlistId: int|null
     * }
     */
    public function decorateMovieDetails(array $movie, array $watchlistIdsByTmdbId): array
    {
        $watchlistId = $watchlistIdsByTmdbId[$movie['id']] ?? null;

        return [
            ...$movie,
            'tmdbId' => $movie['id'],
            'isWatchlisted' => $watchlistId !== null,
            'watchlistId' => $watchlistId,
        ];
    }

    /**
     * @param  Collection<int, Movie>  $movies
     * @return list<array{
     *     id: int,
     *     tmdbId: int,
     *     title: string,
     *     year: int|null,
     *     poster: string,
     *     rating: string,
     *     votes: string,
     *     primaryGenre: string,
     *     overview: string,
     *     href: string,
     *     isWatchlisted: true,
     *     watchlistId: int
     * }>
     */
    private function mapWatchlistMovies(Collection $movies): array
    {
        return $movies
            ->map(fn (Movie $movie): array => [
                'id' => $movie->id,
                'tmdbId' => $movie->tmdb_id,
                'title' => $movie->title,
                'year' => $movie->year,
                'poster' => $movie->poster ?? '',
                'rating' => $movie->rating ?? '',
                'votes' => $movie->votes ?? '',
                'primaryGenre' => $movie->primary_genre ?? 'Movie',
                'overview' => $movie->overview ?? '',
                'href' => $movie->href ?? '#',
                'isWatchlisted' => true,
                'watchlistId' => $movie->id,
            ])
            ->values()
            ->all();
    }
}
