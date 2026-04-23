import { Head, Link, router } from '@inertiajs/react';
import {
    ArrowLeft,
    Bookmark,
    CalendarDays,
    Clock3,
    Languages,
    Star,
    Users,
} from 'lucide-react';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { dashboard } from '@/routes';
import type { MovieCard, MovieDetails } from '@/types/movie';

type MovieShowProps = {
    movie: MovieDetails;
    relatedMovies: MovieCard[];
};

function saveToWatchlist(movie: MovieDetails) {
    router.post(
        '/watchlist',
        {
            tmdb_id: movie.tmdbId,
            title: movie.title,
            year: movie.year,
            poster: movie.poster,
            rating: movie.rating,
            votes: movie.votes,
            primaryGenre: movie.genres[0] ?? 'Movie',
            overview: movie.overview,
            href: movie.href,
        },
        {
            preserveScroll: true,
        },
    );
}

function removeFromWatchlist(watchlistId: number) {
    router.delete(`/watchlist/${watchlistId}`, {
        preserveScroll: true,
    });
}

function DetailStat({
    label,
    value,
    icon: Icon,
}: {
    label: string;
    value: string;
    icon: typeof Clock3;
}) {
    return (
        <Card className="border-border/70 bg-muted/30 shadow-none">
            <CardContent className="flex items-center gap-3 px-4 py-4">
                <div className="rounded-full bg-background p-2 text-muted-foreground">
                    <Icon className="size-4" />
                </div>
                <div>
                    <p className="text-xs tracking-[0.2em] text-muted-foreground uppercase">
                        {label}
                    </p>
                    <p className="font-medium">{value}</p>
                </div>
            </CardContent>
        </Card>
    );
}

export default function MovieShow({ movie, relatedMovies }: MovieShowProps) {
    return (
        <>
            <Head title={movie.title} />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <section className="overflow-hidden rounded-[2rem] border bg-card">
                    <div className="relative aspect-[16/7] min-h-[320px] overflow-hidden">
                        <img
                            src={movie.backdrop}
                            alt={`${movie.title} backdrop`}
                            className="size-full object-cover"
                        />
                        <div className="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/75 to-slate-950/20" />
                        <div className="absolute inset-0 flex flex-col justify-between p-6 text-white md:p-8">
                            <div>
                                <Button
                                    asChild
                                    variant="secondary"
                                    className="bg-white/10 text-white hover:bg-white/20"
                                >
                                    <Link href={dashboard()}>
                                        <ArrowLeft className="size-4" />
                                        Back to search
                                    </Link>
                                </Button>
                            </div>
                            <div className="grid gap-8 lg:grid-cols-[220px_minmax(0,1fr)] lg:items-end">
                                <img
                                    src={movie.poster}
                                    alt={`${movie.title} poster`}
                                    className="hidden w-full max-w-[220px] rounded-2xl border border-white/15 shadow-2xl lg:block"
                                />
                                <div className="space-y-5">
                                    <div className="flex flex-wrap items-center gap-3 text-sm text-white/75">
                                        <span className="inline-flex items-center gap-1.5">
                                            <Star className="size-4 fill-current text-amber-400" />
                                            {movie.rating}
                                        </span>
                                        <span>{movie.year ?? 'TBA'}</span>
                                        <span>{movie.runtime}</span>
                                        <span>{movie.votes} votes</span>
                                    </div>
                                    <div className="space-y-3">
                                        <p className="text-sm tracking-[0.3em] text-white/55 uppercase">
                                            Detail View
                                        </p>
                                        <div className="flex flex-wrap items-center gap-3">
                                            <h1 className="text-4xl font-semibold tracking-tight md:text-5xl">
                                                {movie.title}
                                            </h1>
                                            <Button
                                                type="button"
                                                variant={
                                                    movie.isWatchlisted
                                                        ? 'secondary'
                                                        : 'outline'
                                                }
                                                className="border-white/15 bg-white/10 text-white hover:bg-white/20"
                                                onClick={() =>
                                                    movie.isWatchlisted &&
                                                    movie.watchlistId
                                                        ? removeFromWatchlist(
                                                              movie.watchlistId,
                                                          )
                                                        : saveToWatchlist(movie)
                                                }
                                            >
                                                <Bookmark className="size-4" />
                                                {movie.isWatchlisted
                                                    ? 'Saved to watchlist'
                                                    : 'Save to watchlist'}
                                            </Button>
                                        </div>
                                        <p className="max-w-3xl text-lg text-white/75">
                                            {movie.tagline}
                                        </p>
                                    </div>
                                    <div className="flex flex-wrap gap-2">
                                        {movie.genres.map((genre) => (
                                            <Badge
                                                key={genre}
                                                variant="secondary"
                                                className="bg-white/12 text-white hover:bg-white/12"
                                            >
                                                {genre}
                                            </Badge>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section className="grid gap-6 xl:grid-cols-[minmax(0,1.35fr)_360px]">
                    <div className="space-y-6">
                        <Card>
                            <CardHeader>
                                <Heading
                                    title="Overview"
                                    description="A live TMDB-backed layout for synopsis, metadata, and performer highlights."
                                    variant="small"
                                />
                            </CardHeader>
                            <CardContent className="space-y-5">
                                <p className="text-sm leading-7 text-muted-foreground">
                                    {movie.overview}
                                </p>
                                <div className="grid gap-3 md:grid-cols-2">
                                    <DetailStat
                                        label="Runtime"
                                        value={movie.runtime}
                                        icon={Clock3}
                                    />
                                    <DetailStat
                                        label="Release date"
                                        value={movie.releaseDate}
                                        icon={CalendarDays}
                                    />
                                    <DetailStat
                                        label="Language"
                                        value={movie.language}
                                        icon={Languages}
                                    />
                                    <DetailStat
                                        label="Votes"
                                        value={movie.votes}
                                        icon={Users}
                                    />
                                </div>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle>Cast</CardTitle>
                                <CardDescription>
                                    A quick performer section designed for the
                                    authenticated movie detail screen.
                                </CardDescription>
                            </CardHeader>
                            <CardContent className="grid gap-3 sm:grid-cols-2">
                                {movie.cast.map((actor) => (
                                    <div
                                        key={actor}
                                        className="rounded-xl border border-border/70 bg-muted/25 px-4 py-3"
                                    >
                                        <p className="font-medium">{actor}</p>
                                        <p className="text-sm text-muted-foreground">
                                            Featured cast member
                                        </p>
                                    </div>
                                ))}
                            </CardContent>
                        </Card>
                    </div>

                    <div className="space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle>At a glance</CardTitle>
                                <CardDescription>
                                    The essentials users usually scan first.
                                </CardDescription>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div className="space-y-2 text-sm">
                                    <p className="text-muted-foreground">
                                        Genres
                                    </p>
                                    <div className="flex flex-wrap gap-2">
                                        {movie.genres.map((genre) => (
                                            <Badge
                                                key={genre}
                                                variant="outline"
                                            >
                                                {genre}
                                            </Badge>
                                        ))}
                                    </div>
                                </div>
                                <div className="space-y-1 text-sm">
                                    <p className="text-muted-foreground">
                                        Release date
                                    </p>
                                    <p>{movie.releaseDate}</p>
                                </div>
                                <div className="space-y-1 text-sm">
                                    <p className="text-muted-foreground">
                                        Runtime
                                    </p>
                                    <p>{movie.runtime}</p>
                                </div>
                                <div className="space-y-1 text-sm">
                                    <p className="text-muted-foreground">
                                        Votes
                                    </p>
                                    <p>{movie.votes}</p>
                                </div>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle>Related titles</CardTitle>
                                <CardDescription>
                                    Simple companion cards to extend the browse
                                    flow.
                                </CardDescription>
                            </CardHeader>
                            <CardContent className="space-y-3">
                                {relatedMovies.map((relatedMovie) => (
                                    <Link
                                        key={relatedMovie.tmdbId}
                                        href={relatedMovie.href}
                                        className="flex items-center gap-4 rounded-2xl border border-border/70 p-3 transition-colors hover:bg-muted/40"
                                    >
                                        <img
                                            src={relatedMovie.poster}
                                            alt={`${relatedMovie.title} poster`}
                                            className="h-20 w-14 rounded-lg object-cover"
                                        />
                                        <div className="min-w-0 flex-1">
                                            <p className="truncate font-medium">
                                                {relatedMovie.title}
                                            </p>
                                            <p className="text-sm text-muted-foreground">
                                                {relatedMovie.year ?? 'TBA'} -{' '}
                                                {relatedMovie.primaryGenre}
                                            </p>
                                            <p className="text-sm text-muted-foreground">
                                                {relatedMovie.votes}
                                            </p>
                                        </div>
                                        <Button
                                            type="button"
                                            size="sm"
                                            variant={
                                                relatedMovie.isWatchlisted
                                                    ? 'secondary'
                                                    : 'outline'
                                            }
                                            onClick={(event) => {
                                                event.preventDefault();

                                                if (
                                                    relatedMovie.isWatchlisted &&
                                                    relatedMovie.watchlistId
                                                ) {
                                                    removeFromWatchlist(
                                                        relatedMovie.watchlistId,
                                                    );

                                                    return;
                                                }

                                                router.post(
                                                    '/watchlist',
                                                    {
                                                        tmdb_id:
                                                            relatedMovie.tmdbId,
                                                        title: relatedMovie.title,
                                                        year: relatedMovie.year,
                                                        poster: relatedMovie.poster,
                                                        rating: relatedMovie.rating,
                                                        votes: relatedMovie.votes,
                                                        primaryGenre:
                                                            relatedMovie.primaryGenre,
                                                        overview:
                                                            relatedMovie.overview,
                                                        href: relatedMovie.href,
                                                    },
                                                    {
                                                        preserveScroll: true,
                                                    },
                                                );
                                            }}
                                        >
                                            <Bookmark className="size-4" />
                                            {relatedMovie.isWatchlisted
                                                ? 'Saved'
                                                : 'Save'}
                                        </Button>
                                    </Link>
                                ))}
                            </CardContent>
                        </Card>
                    </div>
                </section>
            </div>
        </>
    );
}

MovieShow.layout = {
    breadcrumbs: [
        {
            title: 'Movie Finder',
            href: dashboard(),
        },
        {
            title: 'Movie Details',
            href: dashboard(),
        },
    ],
};
