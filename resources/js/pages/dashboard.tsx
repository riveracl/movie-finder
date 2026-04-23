import { Head, router, usePage } from '@inertiajs/react';
import { ChevronLeft, ChevronRight, Star } from 'lucide-react';
import { Button } from '@/components/ui/button';
import Heading from '@/components/heading';
import DashboardHero from '@/components/movies/dashboard-hero';
import MovieResultCard from '@/components/movies/movie-result-card';
import WatchlistCard from '@/components/movies/watchlist-card';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { dashboard } from '@/routes';
import type { MovieCard, MovieDetails } from '@/types/movie';

type DashboardProps = {
    search: string;
    movies: MovieCard[];
    featuredMovie: MovieDetails | null;
    watchlistMovies: MovieCard[];
    summary: {
        results: number;
        audienceVotes: string;
        curatedLists: number;
        sourceLabel: string;
    };
    pagination?: {
        currentPage: number;
        totalPages: number;
        totalResults: number;
    };
    errorMessage?: string | null;
};

export default function Dashboard({
    search,
    movies,
    featuredMovie,
    watchlistMovies,
    summary,
    pagination,
    errorMessage,
}: DashboardProps) {
    const { errors } = usePage<{ errors: { search?: string } }>().props;

    return (
        <>
            <Head title="Movie Finder" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <DashboardHero
                    search={search}
                    featuredMovie={featuredMovie}
                    watchlistCount={watchlistMovies.length}
                    summary={summary}
                    searchError={errors.search}
                    errorMessage={errorMessage}
                />

                <section className="grid gap-6 xl:grid-cols-[minmax(0,1.45fr)_400px]">
                    <div className="space-y-4">
                        <Heading
                            title="Search Results"
                            description={`${summary.sourceLabel} with poster-forward cards and a quick read on year, votes, and genre.`}
                        />
                        {movies.length > 0 ? (
                            <div className="space-y-6">
                                <div className="grid gap-4 md:grid-cols-2 2xl:grid-cols-3">
                                    {movies.map((movie) => (
                                        <MovieResultCard
                                            key={movie.tmdbId}
                                            movie={movie}
                                        />
                                    ))}
                                </div>
                                {pagination && pagination.totalPages > 1 && (
                                    <div className="flex items-center justify-between">
                                        <p className="text-sm text-muted-foreground">
                                            Page {pagination.currentPage} of {pagination.totalPages}
                                        </p>
                                        <div className="flex items-center gap-2">
                                            <Button
                                                variant="outline"
                                                size="sm"
                                                disabled={pagination.currentPage <= 1}
                                                onClick={() => router.get(dashboard(), { search, page: pagination.currentPage - 1 }, { preserveScroll: true })}
                                            >
                                                <ChevronLeft className="mr-2 size-4" />
                                                Previous
                                            </Button>
                                            <Button
                                                variant="outline"
                                                size="sm"
                                                disabled={pagination.currentPage >= pagination.totalPages}
                                                onClick={() => router.get(dashboard(), { search, page: pagination.currentPage + 1 }, { preserveScroll: true })}
                                            >
                                                Next
                                                <ChevronRight className="ml-2 size-4" />
                                            </Button>
                                        </div>
                                    </div>
                                )}
                            </div>
                        ) : (
                            <Card>
                                <CardContent className="px-6 py-10 text-center">
                                    <p className="text-lg font-medium">
                                        No movies matched "{search}".
                                    </p>
                                    <p className="mt-2 text-sm text-muted-foreground">
                                        Try a broader title search to repopulate
                                        the result grid.
                                    </p>
                                </CardContent>
                            </Card>
                        )}
                    </div>

                    <div className="space-y-6">
                        <Card className="overflow-hidden pt-0">
                            <div className="relative aspect-[16/10] overflow-hidden">
                                {featuredMovie ? (
                                    <>
                                        <img
                                            src={featuredMovie.backdrop}
                                            alt={`${featuredMovie.title} scene`}
                                            className="size-full object-cover"
                                        />
                                        <div className="absolute inset-0 bg-gradient-to-t from-background via-background/70 to-transparent" />
                                        <div className="absolute inset-x-0 bottom-0 space-y-3 p-6">
                                            <div className="flex items-center gap-2 text-sm text-muted-foreground">
                                                <Star className="size-4 fill-current text-amber-400" />
                                                <span>
                                                    {featuredMovie.rating}
                                                </span>
                                                <span>&middot;</span>
                                                <span>
                                                    {featuredMovie.year ??
                                                        'TBA'}
                                                </span>
                                            </div>
                                            <h2 className="text-2xl font-semibold">
                                                {featuredMovie.title}
                                            </h2>
                                        </div>
                                    </>
                                ) : (
                                    <div className="flex size-full items-center justify-center bg-muted/40 p-6 text-center">
                                        <p className="max-w-sm text-sm text-muted-foreground">
                                            Featured movie insights will appear
                                            here after a successful TMDB
                                            response.
                                        </p>
                                    </div>
                                )}
                            </div>
                            <CardContent className="space-y-6 px-6 py-6">
                                {featuredMovie ? (
                                    <>
                                        <div>
                                            <p className="text-sm leading-6 text-muted-foreground">
                                                {featuredMovie.overview}
                                            </p>
                                        </div>
                                        <div className="space-y-3">
                                            <p className="text-sm font-medium">
                                                Included in the full detail view
                                            </p>
                                            <div className="flex flex-wrap gap-2">
                                                {featuredMovie.genres.map(
                                                    (genre) => (
                                                        <Badge
                                                            key={genre}
                                                            variant="outline"
                                                        >
                                                            {genre}
                                                        </Badge>
                                                    ),
                                                )}
                                            </div>
                                            <p className="text-sm text-muted-foreground">
                                                Cast:{' '}
                                                {featuredMovie.cast.join(', ')}
                                            </p>
                                        </div>
                                    </>
                                ) : (
                                    <p className="text-sm text-muted-foreground">
                                        Search results and detail metadata will
                                        render here once TMDB responds.
                                    </p>
                                )}
                            </CardContent>
                        </Card>

                        <Card>
                            <CardContent className="space-y-4 px-6 py-6">
                                <Heading
                                    title="Your Watchlist"
                                    description="Saved titles persist for the authenticated user."
                                    variant="small"
                                />
                                {watchlistMovies.length > 0 ? (
                                    <div className="space-y-3">
                                        {watchlistMovies.map((movie) => (
                                            <WatchlistCard
                                                key={
                                                    movie.watchlistId ??
                                                    movie.tmdbId
                                                }
                                                movie={movie}
                                            />
                                        ))}
                                    </div>
                                ) : (
                                    <p className="text-sm text-muted-foreground">
                                        Save a movie from the results or detail
                                        view to start your watchlist.
                                    </p>
                                )}
                            </CardContent>
                        </Card>
                    </div>
                </section>
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [
        {
            title: 'Movie Finder',
            href: dashboard(),
        },
    ],
};
