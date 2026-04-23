<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected away from movie details', function () {
    $this->get(route('movies.show', 'interstellar'))
        ->assertRedirect(route('login'));
});

test('authenticated users can view a movie detail page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('movies.show', 'interstellar'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('movies/show')
            ->where('movie.slug', 'interstellar')
            ->where('movie.title', 'Interstellar')
            ->where('movie.runtime', '169 min')
            ->where('movie.releaseDate', '2014-11-07')
            ->where('movie.genres.0', 'Science Fiction')
            ->where('movie.cast.0', 'Matthew McConaughey')
            ->has('relatedMovies', 3),
        );
});

test('unknown movie detail pages return a 404 response', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('movies.show', 'unknown-movie'))
        ->assertNotFound();
});
