import { Head, Link } from '@inertiajs/react';
import { Clapperboard, Search, Sparkles, Star } from 'lucide-react';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { dashboard } from '@/routes';
import type { MovieCard, MovieDetails } from '@/types/movie';

type DashboardProps = {
    search: string;
    movies: MovieCard[];
    featuredMovie: MovieDetails;
    summary: {
        results: number;
        averageVotes: string;
        curatedLists: number;
    };
};

function MetricCard({
    label,
    value,
    icon: Icon,
}: {
    label: string;
    value: string;
    icon: typeof Search;
}) {
    return (
        <Card className="border-white/15 bg-white/10 shadow-none backdrop-blur-sm dark:bg-white/5">
            <CardContent className="flex items-center gap-4 px-5 py-4">
                <div className="rounded-full bg-white/15 p-2.5 text-white">
                    <Icon className="size-4" />
                </div>
                <div>
                    <p className="text-xs tracking-[0.25em] text-white/70 uppercase">
                        {label}
                    </p>
                    <p className="text-lg font-semibold text-white">{value}</p>
                </div>
            </CardContent>
        </Card>
    );
}

function MovieResultCard({ movie }: { movie: MovieCard }) {
    return (
        <Card className="group overflow-hidden border-border/70 pt-0 transition-transform duration-200 hover:-translate-y-1">
            <div className="relative aspect-[4/5] overflow-hidden">
                <img
                    src={movie.poster}
                    alt={`${movie.title} poster`}
                    className="size-full object-cover transition duration-300 group-hover:scale-[1.03]"
                />
                <div className="absolute inset-x-0 top-0 flex items-center justify-between p-4">
                    <Badge className="bg-black/75 text-white hover:bg-black/75">
                        {movie.primaryGenre}
                    </Badge>
                    <Badge
                        variant="secondary"
                        className="bg-white/90 text-black hover:bg-white/90"
                    >
                        <Star className="size-3 fill-current" />
                        {movie.rating}
                    </Badge>
                </div>
            </div>
            <CardContent className="space-y-4 px-5 py-5">
                <div className="space-y-1">
                    <div className="flex items-start justify-between gap-3">
                        <CardTitle className="text-lg">{movie.title}</CardTitle>
                        <span className="text-sm text-muted-foreground">
                            {movie.year}
                        </span>
                    </div>
                    <CardDescription className="line-clamp-3">
                        {movie.overview}
                    </CardDescription>
                </div>
                <div className="flex items-center justify-between gap-3">
                    <p className="text-sm text-muted-foreground">
                        {movie.votes}
                    </p>
                    <Button asChild size="sm">
                        <Link href={movie.href}>View details</Link>
                    </Button>
                </div>
            </CardContent>
        </Card>
    );
}

export default function Dashboard({
    search,
    movies,
    featuredMovie,
    summary,
}: DashboardProps) {
    return (
        <>
            <Head title="Movie Finder" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <section className="overflow-hidden rounded-[2rem] bg-[radial-gradient(circle_at_top_left,_rgba(255,255,255,0.18),_transparent_35%),linear-gradient(135deg,_#0f172a_0%,_#172554_45%,_#312e81_100%)] text-white shadow-xl">
                    <div className="grid gap-10 p-6 md:p-8 xl:grid-cols-[minmax(0,1.2fr)_360px]">
                        <div className="space-y-8">
                            <div className="space-y-4">
                                <Badge className="border border-white/15 bg-white/10 text-white hover:bg-white/10">
                                    Curated for authenticated movie fans
                                </Badge>
                                <div className="space-y-3">
                                    <h1 className="max-w-2xl text-4xl font-semibold tracking-tight md:text-5xl">
                                        Search titles fast, then settle into a
                                        detail view built for browsing.
                                    </h1>
                                    <p className="max-w-2xl text-base text-white/75">
                                        This static authenticated experience
                                        combines a lightweight title search,
                                        clear result cards, and a cinematic
                                        detail screen for deeper exploration.
                                    </p>
                                </div>
                            </div>

                            <form
                                action={dashboard().url}
                                method="get"
                                className="flex flex-col gap-3 rounded-2xl border border-white/15 bg-black/20 p-3 backdrop-blur-sm md:flex-row"
                            >
                                <div className="relative flex-1">
                                    <Search className="absolute top-1/2 left-4 size-4 -translate-y-1/2 text-white/50" />
                                    <Input
                                        name="search"
                                        defaultValue={search}
                                        placeholder="Search by movie title"
                                        className="h-12 border-white/15 bg-white/10 pl-11 text-white placeholder:text-white/45"
                                    />
                                </div>
                                <Button
                                    type="submit"
                                    size="lg"
                                    className="bg-white text-slate-950 hover:bg-white/90"
                                >
                                    Find movies
                                </Button>
                            </form>

                            <div className="grid gap-3 md:grid-cols-3">
                                <MetricCard
                                    label="Results"
                                    value={summary.results.toString()}
                                    icon={Clapperboard}
                                />
                                <MetricCard
                                    label="Audience votes"
                                    value={summary.averageVotes}
                                    icon={Star}
                                />
                                <MetricCard
                                    label="Curated lists"
                                    value={summary.curatedLists.toString()}
                                    icon={Sparkles}
                                />
                            </div>
                        </div>

                        <Card className="overflow-hidden border-white/15 bg-white/10 pt-0 text-white shadow-none backdrop-blur-sm">
                            <div className="relative aspect-[4/3]">
                                <img
                                    src={featuredMovie.backdrop}
                                    alt={`${featuredMovie.title} backdrop`}
                                    className="size-full object-cover"
                                />
                                <div className="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/45 to-transparent" />
                                <div className="absolute inset-x-0 bottom-0 space-y-3 p-5">
                                    <div className="flex flex-wrap gap-2">
                                        {featuredMovie.genres.map((genre) => (
                                            <Badge
                                                key={genre}
                                                variant="secondary"
                                                className="bg-white/15 text-white"
                                            >
                                                {genre}
                                            </Badge>
                                        ))}
                                    </div>
                                    <div>
                                        <p className="text-xs tracking-[0.3em] text-white/60 uppercase">
                                            Featured detail preview
                                        </p>
                                        <h2 className="text-2xl font-semibold">
                                            {featuredMovie.title}
                                        </h2>
                                        <p className="text-sm text-white/70">
                                            {featuredMovie.tagline}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <CardContent className="grid gap-5 px-5 py-5">
                                <div className="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <p className="text-white/55">Runtime</p>
                                        <p className="font-medium">
                                            {featuredMovie.runtime}
                                        </p>
                                    </div>
                                    <div>
                                        <p className="text-white/55">Votes</p>
                                        <p className="font-medium">
                                            {featuredMovie.votes}
                                        </p>
                                    </div>
                                    <div>
                                        <p className="text-white/55">Release</p>
                                        <p className="font-medium">
                                            {featuredMovie.releaseDate}
                                        </p>
                                    </div>
                                    <div>
                                        <p className="text-white/55">
                                            Language
                                        </p>
                                        <p className="font-medium">
                                            {featuredMovie.language}
                                        </p>
                                    </div>
                                </div>
                                <Button asChild variant="secondary">
                                    <Link href={featuredMovie.href}>
                                        Open full detail view
                                    </Link>
                                </Button>
                            </CardContent>
                        </Card>
                    </div>
                </section>

                <section className="grid gap-6 xl:grid-cols-[minmax(0,1.45fr)_400px]">
                    <div className="space-y-4">
                        <Heading
                            title="Search Results"
                            description="Poster-forward cards with a quick read on year, votes, and genre."
                        />
                        {movies.length > 0 ? (
                            <div className="grid gap-4 md:grid-cols-2 2xl:grid-cols-3">
                                {movies.map((movie) => (
                                    <MovieResultCard
                                        key={movie.slug}
                                        movie={movie}
                                    />
                                ))}
                            </div>
                        ) : (
                            <Card>
                                <CardContent className="px-6 py-10 text-center">
                                    <p className="text-lg font-medium">
                                        No movies matched "{search}".
                                    </p>
                                    <p className="mt-2 text-sm text-muted-foreground">
                                        Try a broader title search to repopulate
                                        the static result grid.
                                    </p>
                                </CardContent>
                            </Card>
                        )}
                    </div>

                    <Card className="overflow-hidden pt-0">
                        <div className="relative aspect-[16/10] overflow-hidden">
                            <img
                                src={featuredMovie.backdrop}
                                alt={`${featuredMovie.title} scene`}
                                className="size-full object-cover"
                            />
                            <div className="absolute inset-0 bg-gradient-to-t from-background via-background/70 to-transparent" />
                            <div className="absolute inset-x-0 bottom-0 space-y-3 p-6">
                                <div className="flex items-center gap-2 text-sm text-muted-foreground">
                                    <Star className="size-4 fill-current text-amber-400" />
                                    <span>{featuredMovie.rating}</span>
                                    <span>&middot;</span>
                                    <span>{featuredMovie.year}</span>
                                </div>
                                <h2 className="text-2xl font-semibold">
                                    {featuredMovie.title}
                                </h2>
                            </div>
                        </div>
                        <CardContent className="space-y-6 px-6 py-6">
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
                                    {featuredMovie.genres.map((genre) => (
                                        <Badge key={genre} variant="outline">
                                            {genre}
                                        </Badge>
                                    ))}
                                </div>
                                <p className="text-sm text-muted-foreground">
                                    Cast: {featuredMovie.cast.join(', ')}
                                </p>
                            </div>
                        </CardContent>
                    </Card>
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
