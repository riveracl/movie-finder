<?php

namespace App\Http\Controllers;

use App\Http\Requests\MovieSearchRequest;
use App\Services\TmdbMovieService;
use App\Services\TmdbServiceException;
use App\Services\WatchlistService;
use Inertia\Inertia;
use Inertia\Response;

class MovieController extends Controller
{
    public function index(
        MovieSearchRequest $request,
        TmdbMovieService $tmdbMovieService,
        WatchlistService $watchlistService,
    ): Response {
        $search = $request->search();
        $watchlistIdsByTmdbId = $watchlistService->idsByTmdbIdFor($request->user());

        try {
            $discovery = $tmdbMovieService->discoverMovies($search);
            $movies = $watchlistService->decorateMovieCards(
                $discovery['movies'],
                $watchlistIdsByTmdbId,
            );
            $featuredMovie = $movies !== []
                ? $watchlistService->decorateMovieDetails(
                    $tmdbMovieService->getMovieDetails($movies[0]['tmdbId']) ?? throw new TmdbServiceException('Featured movie details were unavailable.'),
                    $watchlistIdsByTmdbId,
                )
                : null;
            $errorMessage = null;
        } catch (TmdbServiceException $exception) {
            report($exception);

            $movies = [];
            $featuredMovie = null;
            $errorMessage = 'We could not load TMDB data right now. Please try again in a moment.';
            $discovery = [
                'summary' => [
                    'results' => 0,
                    'audienceVotes' => '0 votes',
                    'curatedLists' => 12,
                    'sourceLabel' => 'TMDB unavailable',
                ],
            ];
        }

        return Inertia::render('dashboard', [
            'search' => $search ?? '',
            'movies' => $movies,
            'featuredMovie' => $featuredMovie,
            'watchlistMovies' => $watchlistService->itemsFor($request->user()),
            'summary' => $discovery['summary'],
            'errorMessage' => $errorMessage,
        ]);
    }

    public function show(
        int $movie,
        TmdbMovieService $tmdbMovieService,
        WatchlistService $watchlistService,
    ) {
        try {
            $selectedMovie = $tmdbMovieService->getMovieDetails($movie);

            abort_if($selectedMovie === null, 404);
            $watchlistIdsByTmdbId = $watchlistService->idsByTmdbIdFor(request()->user());

            return Inertia::render('movies/show', [
                'movie' => $watchlistService->decorateMovieDetails(
                    $selectedMovie,
                    $watchlistIdsByTmdbId,
                ),
                'relatedMovies' => $watchlistService->decorateMovieCards(
                    $tmdbMovieService->recommendedMovies($movie),
                    $watchlistIdsByTmdbId,
                ),
            ]);
        } catch (TmdbServiceException $exception) {
            report($exception);

            Inertia::flash('toast', [
                'type' => 'error',
                'message' => 'We could not load that movie right now. Please try again.',
            ]);

            return redirect()->route('dashboard');
        }
    }
}
