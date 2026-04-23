import { Link } from '@inertiajs/react';
import { Star } from 'lucide-react';
import WatchlistButton from '@/components/movies/watchlist-button';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardTitle,
} from '@/components/ui/card';
import type { MovieResultCardProps } from '@/types';

export default function MovieResultCard({ movie }: MovieResultCardProps) {
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
                            {movie.year ?? 'TBA'}
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
                    <div className="flex items-center gap-2">
                        <WatchlistButton movie={movie} />
                        <Button asChild size="sm">
                            <Link href={movie.href}>View details</Link>
                        </Button>
                    </div>
                </div>
            </CardContent>
        </Card>
    );
}
