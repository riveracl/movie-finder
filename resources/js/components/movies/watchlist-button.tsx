import { Bookmark } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { removeFromWatchlist, saveToWatchlist } from '@/lib/watchlist';
import type { WatchlistButtonProps } from '@/types';

export default function WatchlistButton({
    movie,
    className,
    savedLabel = 'Saved',
    saveLabel = 'Save',
    variant,
}: WatchlistButtonProps) {
    return (
        <Button
            type="button"
            variant={variant ?? (movie.isWatchlisted ? 'secondary' : 'outline')}
            size="sm"
            className={className}
            onClick={() =>
                movie.isWatchlisted && movie.watchlistId
                    ? removeFromWatchlist(movie.watchlistId)
                    : saveToWatchlist(movie)
            }
        >
            <Bookmark className="size-4" />
            {movie.isWatchlisted ? savedLabel : saveLabel}
        </Button>
    );
}
