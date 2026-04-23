export type MovieCard = {
    slug: string;
    title: string;
    year: number;
    poster: string;
    rating: string;
    votes: string;
    primaryGenre: string;
    overview: string;
    href: string;
};

export type MovieDetails = {
    slug: string;
    title: string;
    year: number;
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
};
