import { router } from '@inertiajs/react';

export type WatchlistPayload = {
    tmdbId: number;
    title: string;
    year: number | null;
    poster: string;
    rating: string;
    votes: string;
    primaryGenre: string;
    overview: string;
    href: string;
};

export function saveToWatchlist(movie: WatchlistPayload): void {
    router.post(
        '/watchlist',
        {
            tmdb_id: movie.tmdbId,
            title: movie.title,
            year: movie.year,
            poster: movie.poster,
            rating: movie.rating,
            votes: movie.votes,
            primaryGenre: movie.primaryGenre,
            overview: movie.overview,
            href: movie.href,
        },
        {
            preserveScroll: true,
        },
    );
}

export function removeFromWatchlist(watchlistId: number): void {
    router.delete(`/watchlist/${watchlistId}`, {
        preserveScroll: true,
    });
}
