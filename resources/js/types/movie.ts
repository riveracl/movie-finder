export type MovieCard = {
    id: number;
    tmdbId: number;
    title: string;
    year: number | null;
    poster: string;
    rating: string;
    votes: string;
    primaryGenre: string;
    overview: string;
    href: string;
    isWatchlisted: boolean;
    watchlistId: number | null;
};

export type MovieDetails = {
    id: number;
    tmdbId: number;
    title: string;
    year: number | null;
    rating: string;
    votes: string;
    runtime: string;
    releaseDate: string;
    poster: string;
    backdrop: string;
    href: string;
    genres: string[];
    cast: string[];
    overview: string;
    tagline: string;
    language: string;
    isWatchlisted: boolean;
    watchlistId: number | null;
};
