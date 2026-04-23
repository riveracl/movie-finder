<?php

use App\Http\Requests\MovieSearchRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

uses(TestCase::class);

test('movie search requires at least two characters when present', function () {
    $request = new MovieSearchRequest;

    $validator = Validator::make(
        ['search' => 'a'],
        $request->rules(),
    );

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('search'))->toBeTrue();
});

test('movie search allows null and reasonable title input', function () {
    $request = new MovieSearchRequest;

    $validator = Validator::make(
        ['search' => 'Interstellar'],
        $request->rules(),
    );

    expect($validator->fails())->toBeFalse();
});
