import type { LucideIcon } from 'lucide-react';
import type { WatchlistPayload } from '@/lib/watchlist';
import type { MovieCard, MovieDetails } from '@/types/movie';

export type WatchlistButtonProps = {
    movie: WatchlistPayload & {
        isWatchlisted: boolean;
        watchlistId: number | null;
    };
    className?: string;
    savedLabel?: string;
    saveLabel?: string;
    variant?: 'secondary' | 'outline';
};

export type DashboardHeroProps = {
    search: string;
    featuredMovie: MovieDetails | null;
    watchlistCount: number;
    summary: {
        results: number;
        audienceVotes: string;
    };
    searchError?: string;
    errorMessage?: string | null;
};

export type MetricCardProps = {
    label: string;
    value: string;
    icon: LucideIcon;
};

export type MovieResultCardProps = {
    movie: MovieCard;
};

export type WatchlistCardProps = {
    movie: MovieCard;
};
