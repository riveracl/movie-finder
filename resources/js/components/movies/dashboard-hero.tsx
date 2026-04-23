import { Link } from '@inertiajs/react';
import { Bookmark, Clapperboard, Search, Star } from 'lucide-react';
import AlertError from '@/components/alert-error';
import InputError from '@/components/input-error';
import MetricCard from '@/components/movies/metric-card';
import WatchlistButton from '@/components/movies/watchlist-button';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { dashboard } from '@/routes';
import type { DashboardHeroProps } from '@/types';

export default function DashboardHero({
    search,
    featuredMovie,
    watchlistCount,
    summary,
    searchError,
    errorMessage,
}: DashboardHeroProps) {
    return (
        <section className="overflow-hidden rounded-[2rem] bg-[radial-gradient(circle_at_top_left,_rgba(255,255,255,0.18),_transparent_35%),linear-gradient(135deg,_#0f172a_0%,_#172554_45%,_#312e81_100%)] text-white shadow-xl">
            <div className="grid gap-10 p-6 md:p-8 xl:grid-cols-[minmax(0,1.2fr)_360px]">
                <div className="space-y-8">
                    <div className="space-y-4">
                        <Badge className="border border-white/15 bg-white/10 text-white hover:bg-white/10">
                            Curated for authenticated movie fans
                        </Badge>
                        <div className="space-y-3">
                            <h1 className="max-w-2xl text-4xl font-semibold tracking-tight md:text-5xl">
                                Search titles fast, then build a personal
                                watchlist as you browse.
                            </h1>
                            <p className="max-w-2xl text-base text-white/75">
                                Discover movies in real time, save what catches your eye, and build a watchlist that’s all yours.
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

                    <InputError
                        message={searchError}
                        className="text-white/85"
                    />

                    {errorMessage && (
                        <AlertError
                            title="TMDB is unavailable"
                            errors={[errorMessage]}
                        />
                    )}

                    <div className="grid gap-3 md:grid-cols-3">
                        <MetricCard
                            label="Results"
                            value={summary.results.toString()}
                            icon={Clapperboard}
                        />
                        <MetricCard
                            label="Audience votes"
                            value={summary.audienceVotes}
                            icon={Star}
                        />
                        <MetricCard
                            label="Watchlist"
                            value={watchlistCount.toString()}
                            icon={Bookmark}
                        />
                    </div>
                </div>

                {featuredMovie ? (
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
                                    <p className="text-white/55">Language</p>
                                    <p className="font-medium">
                                        {featuredMovie.language}
                                    </p>
                                </div>
                            </div>
                            <div className="flex items-center gap-2">
                                <WatchlistButton
                                    movie={{
                                        tmdbId: featuredMovie.tmdbId,
                                        title: featuredMovie.title,
                                        year: featuredMovie.year,
                                        poster: featuredMovie.poster,
                                        rating: featuredMovie.rating,
                                        votes: featuredMovie.votes,
                                        primaryGenre:
                                            featuredMovie.genres[0] ?? 'Movie',
                                        overview: featuredMovie.overview,
                                        href: featuredMovie.href,
                                        isWatchlisted:
                                            featuredMovie.isWatchlisted,
                                        watchlistId: featuredMovie.watchlistId,
                                    }}
                                    className="border-white/15 bg-white/10 text-white hover:bg-white/20"
                                />
                                <Button asChild variant="secondary">
                                    <Link href={featuredMovie.href}>
                                        Open full detail view
                                    </Link>
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                ) : (
                    <Card className="border-white/15 bg-white/10 text-white shadow-none backdrop-blur-sm">
                        <CardContent className="px-5 py-8">
                            <p className="text-lg font-medium">
                                No featured movie available yet.
                            </p>
                            <p className="mt-2 text-sm text-white/70">
                                Try another title or retry once TMDB is
                                reachable again.
                            </p>
                        </CardContent>
                    </Card>
                )}
            </div>
        </section>
    );
}
