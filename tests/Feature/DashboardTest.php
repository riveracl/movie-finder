<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('dashboard')
            ->where('search', '')
            ->where('featuredMovie.slug', 'interstellar')
            ->has('movies', 5)
            ->where('movies.0.slug', 'interstellar')
            ->where('summary.results', 5),
        );
});

test('dashboard search filters movies by title', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard', ['search' => 'dark']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('dashboard')
            ->where('search', 'dark')
            ->where('featuredMovie.slug', 'the-dark-knight')
            ->has('movies', 1)
            ->where('movies.0.slug', 'the-dark-knight')
            ->where('summary.results', 1),
        );
});
