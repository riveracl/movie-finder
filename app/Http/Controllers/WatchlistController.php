<?php

namespace App\Http\Controllers;

use App\Http\Requests\ToggleWatchlistRequest;
use App\Models\Movie;
use App\Services\WatchlistService;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class WatchlistController extends Controller
{
    public function store(
        ToggleWatchlistRequest $request,
        WatchlistService $watchlistService,
    ): Response {
        $movie = $watchlistService->add($request->user(), $request->movieData());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => "{$movie->title} added to your watchlist.",
        ]);

        return back(status: Response::HTTP_SEE_OTHER);
    }

    public function destroy(
        Movie $movie,
        WatchlistService $watchlistService,
    ): Response {
        $watchlistService->remove(request()->user(), $movie);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => "{$movie->title} removed from your watchlist.",
        ]);

        return back(status: Response::HTTP_SEE_OTHER);
    }
}
