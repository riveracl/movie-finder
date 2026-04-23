<?php

use App\Http\Controllers\MovieController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [MovieController::class, 'index'])->name('dashboard');
    Route::get('movies/{movie}', [MovieController::class, 'show'])->name('movies.show');
});

require __DIR__.'/settings.php';
