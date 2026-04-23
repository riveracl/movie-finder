<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class MovieController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->string('search')->value());
        $movies = $this->filterMovies($this->catalog(), $search);
        $featuredMovie = $movies[0] ?? $this->catalog()[0];

        return Inertia::render('dashboard', [
            'search' => $search,
            'movies' => array_map(fn (array $movie): array => $this->toMovieCard($movie), $movies),
            'featuredMovie' => $this->toMovieDetails($featuredMovie),
            'summary' => [
                'results' => count($movies),
                'averageVotes' => $this->formatVoteCount($movies),
                'curatedLists' => 12,
            ],
        ]);
    }

    public function show(string $movie): Response
    {
        $selectedMovie = collect($this->catalog())
            ->first(fn (array $catalogMovie): bool => $catalogMovie['slug'] === $movie);

        abort_if($selectedMovie === null, 404);

        $relatedMovies = collect($this->catalog())
            ->reject(fn (array $catalogMovie): bool => $catalogMovie['slug'] === $movie)
            ->take(3)
            ->map(fn (array $catalogMovie): array => $this->toMovieCard($catalogMovie))
            ->values()
            ->all();

        return Inertia::render('movies/show', [
            'movie' => $this->toMovieDetails($selectedMovie),
            'relatedMovies' => $relatedMovies,
        ]);
    }

    /**
     * @return list<array{
     *     slug: string,
     *     title: string,
     *     year: int,
     *     rating: float,
     *     votes: int,
     *     runtime: int,
     *     release_date: string,
     *     poster: string,
     *     backdrop: string,
     *     genres: list<string>,
     *     cast: list<string>,
     *     overview: string,
     *     tagline: string,
     *     language: string
     * }>
     */
    private function catalog(): array
    {
        return [
            [
                'slug' => 'interstellar',
                'title' => 'Interstellar',
                'year' => 2014,
                'rating' => 8.7,
                'votes' => 2134500,
                'runtime' => 169,
                'release_date' => '2014-11-07',
                'poster' => 'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/w1280/rAiYTfKGqDCRIIqo664sY9XZIvQ.jpg',
                'genres' => ['Science Fiction', 'Adventure', 'Drama'],
                'cast' => ['Matthew McConaughey', 'Anne Hathaway', 'Jessica Chastain', 'Michael Caine'],
                'overview' => 'When Earth becomes increasingly uninhabitable, a former NASA pilot leads a mission through a wormhole to search for a new home for humanity.',
                'tagline' => 'Mankind was born on Earth. It was never meant to die here.',
                'language' => 'English',
            ],
            [
                'slug' => 'the-dark-knight',
                'title' => 'The Dark Knight',
                'year' => 2008,
                'rating' => 9.0,
                'votes' => 2967300,
                'runtime' => 152,
                'release_date' => '2008-07-18',
                'poster' => 'https://image.tmdb.org/t/p/w500/qJ2tW6WMUDux911r6m7haRef0WH.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/w1280/nMKdUUepR0i5zn0y1T4CsSB5chy.jpg',
                'genres' => ['Drama', 'Action', 'Crime'],
                'cast' => ['Christian Bale', 'Heath Ledger', 'Aaron Eckhart', 'Gary Oldman'],
                'overview' => 'Batman raises the stakes in his war on crime and faces the Joker, a criminal mastermind who unleashes chaos across Gotham City.',
                'tagline' => 'Welcome to a world without rules.',
                'language' => 'English',
            ],
            [
                'slug' => 'parasite',
                'title' => 'Parasite',
                'year' => 2019,
                'rating' => 8.5,
                'votes' => 1042200,
                'runtime' => 133,
                'release_date' => '2019-11-08',
                'poster' => 'https://image.tmdb.org/t/p/w500/7IiTTgloJzvGI1TAYymCfbfl3vT.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/w1280/TU9NIjwzjoKPwQHoHshkFcQUCG.jpg',
                'genres' => ['Comedy', 'Thriller', 'Drama'],
                'cast' => ['Song Kang-ho', 'Choi Woo-shik', 'Park So-dam', 'Cho Yeo-jeong'],
                'overview' => 'A cash-strapped family insinuates themselves into the lives of a wealthy household until greed and class resentment twist everything out of control.',
                'tagline' => 'Act like you own the place.',
                'language' => 'Korean',
            ],
            [
                'slug' => 'dune-part-two',
                'title' => 'Dune: Part Two',
                'year' => 2024,
                'rating' => 8.5,
                'votes' => 972400,
                'runtime' => 166,
                'release_date' => '2024-03-01',
                'poster' => 'https://image.tmdb.org/t/p/w500/8b8R8l88Qje9dn9OE8PY05Nxl1X.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/w1280/6z4mWfE2U9nT4m4vZQZ1Eg7dW4B.jpg',
                'genres' => ['Science Fiction', 'Adventure', 'Drama'],
                'cast' => ['Timothee Chalamet', 'Zendaya', 'Rebecca Ferguson', 'Austin Butler'],
                'overview' => 'Paul Atreides embraces his destiny among the Fremen and wages war against the forces that destroyed his family.',
                'tagline' => 'Long live the fighters.',
                'language' => 'English',
            ],
            [
                'slug' => 'spider-man-into-the-spider-verse',
                'title' => 'Spider-Man: Into the Spider-Verse',
                'year' => 2018,
                'rating' => 8.4,
                'votes' => 678900,
                'runtime' => 117,
                'release_date' => '2018-12-14',
                'poster' => 'https://image.tmdb.org/t/p/w500/iiZZdoQBEYBv6id8su7ImL0oCbD.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/w1280/3bWUP9kyf9BxVc0hmZdqXB2w4UP.jpg',
                'genres' => ['Animation', 'Action', 'Adventure'],
                'cast' => ['Shameik Moore', 'Hailee Steinfeld', 'Mahershala Ali', 'Jake Johnson'],
                'overview' => 'Miles Morales becomes Spider-Man and discovers that he is not alone when heroes from multiple dimensions collide.',
                'tagline' => 'More than one wears the mask.',
                'language' => 'English',
            ],
        ];
    }

    /**
     * @param  list<array{
     *     slug: string,
     *     title: string,
     *     year: int,
     *     rating: float,
     *     votes: int,
     *     runtime: int,
     *     release_date: string,
     *     poster: string,
     *     backdrop: string,
     *     genres: list<string>,
     *     cast: list<string>,
     *     overview: string,
     *     tagline: string,
     *     language: string
     * }>  $movies
     * @return list<array{
     *     slug: string,
     *     title: string,
     *     year: int,
     *     rating: float,
     *     votes: int,
     *     runtime: int,
     *     release_date: string,
     *     poster: string,
     *     backdrop: string,
     *     genres: list<string>,
     *     cast: list<string>,
     *     overview: string,
     *     tagline: string,
     *     language: string
     * }>
     */
    private function filterMovies(array $movies, string $search): array
    {
        if ($search === '') {
            return $movies;
        }

        return array_values(array_filter(
            $movies,
            fn (array $movie): bool => Str::contains(
                Str::lower($movie['title']),
                Str::lower($search),
            ),
        ));
    }

    /**
     * @param  array{
     *     slug: string,
     *     title: string,
     *     year: int,
     *     rating: float,
     *     votes: int,
     *     runtime: int,
     *     release_date: string,
     *     poster: string,
     *     backdrop: string,
     *     genres: list<string>,
     *     cast: list<string>,
     *     overview: string,
     *     tagline: string,
     *     language: string
     * }  $movie
     * @return array{
     *     slug: string,
     *     title: string,
     *     year: int,
     *     poster: string,
     *     rating: string,
     *     votes: string,
     *     primaryGenre: string,
     *     overview: string,
     *     href: string
     * }
     */
    private function toMovieCard(array $movie): array
    {
        return [
            'slug' => $movie['slug'],
            'title' => $movie['title'],
            'year' => $movie['year'],
            'poster' => $movie['poster'],
            'rating' => number_format($movie['rating'], 1),
            'votes' => number_format($movie['votes'] / 1000000, 1).'M votes',
            'primaryGenre' => $movie['genres'][0],
            'overview' => $movie['overview'],
            'href' => route('movies.show', $movie['slug']),
        ];
    }

    /**
     * @param  array{
     *     slug: string,
     *     title: string,
     *     year: int,
     *     rating: float,
     *     votes: int,
     *     runtime: int,
     *     release_date: string,
     *     poster: string,
     *     backdrop: string,
     *     genres: list<string>,
     *     cast: list<string>,
     *     overview: string,
     *     tagline: string,
     *     language: string
     * }  $movie
     * @return array{
     *     slug: string,
     *     title: string,
     *     year: int,
     *     rating: string,
     *     votes: string,
     *     runtime: string,
     *     releaseDate: string,
     *     poster: string,
     *     backdrop: string,
     *     href: string,
     *     genres: list<string>,
     *     cast: list<string>,
     *     overview: string,
     *     tagline: string,
     *     language: string
     * }
     */
    private function toMovieDetails(array $movie): array
    {
        return [
            'slug' => $movie['slug'],
            'title' => $movie['title'],
            'year' => $movie['year'],
            'rating' => number_format($movie['rating'], 1),
            'votes' => number_format($movie['votes']),
            'runtime' => $movie['runtime'].' min',
            'releaseDate' => $movie['release_date'],
            'poster' => $movie['poster'],
            'backdrop' => $movie['backdrop'],
            'href' => route('movies.show', $movie['slug']),
            'genres' => $movie['genres'],
            'cast' => $movie['cast'],
            'overview' => $movie['overview'],
            'tagline' => $movie['tagline'],
            'language' => $movie['language'],
        ];
    }

    /**
     * @param  list<array{
     *     slug: string,
     *     title: string,
     *     year: int,
     *     rating: float,
     *     votes: int,
     *     runtime: int,
     *     release_date: string,
     *     poster: string,
     *     backdrop: string,
     *     genres: list<string>,
     *     cast: list<string>,
     *     overview: string,
     *     tagline: string,
     *     language: string
     * }>  $movies
     */
    private function formatVoteCount(array $movies): string
    {
        if ($movies === []) {
            return '0 tracked';
        }

        $votes = array_sum(array_map(
            fn (array $movie): int => $movie['votes'],
            $movies,
        ));

        return number_format($votes / 1000000, 1).'M tracked';
    }
}
