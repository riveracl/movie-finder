import { Link } from '@inertiajs/react';
import WatchlistButton from '@/components/movies/watchlist-button';
import type { WatchlistCardProps } from '@/types';

export default function WatchlistCard({ movie }: WatchlistCardProps) {
    return (
        <Link
            href={movie.href}
            className="flex items-center gap-4 rounded-2xl border border-border/70 p-3 transition-colors hover:bg-muted/40"
        >
            <img
                src={movie.poster}
                alt={`${movie.title} poster`}
                className="h-20 w-14 rounded-lg object-cover"
            />
            <div className="min-w-0 flex-1">
                <p className="truncate font-medium">{movie.title}</p>
                <p className="text-sm text-muted-foreground">
                    {movie.year ?? 'TBA'} - {movie.primaryGenre}
                </p>
                <p className="line-clamp-2 text-sm text-muted-foreground">
                    {movie.overview}
                </p>
            </div>
            {movie.watchlistId && (
                <div
                    onClick={(event) => {
                        event.preventDefault();
                    }}
                >
                    <WatchlistButton
                        movie={movie}
                        savedLabel="Remove"
                        saveLabel="Save"
                        variant="outline"
                    />
                </div>
            )}
        </Link>
    );
}
