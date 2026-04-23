<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ToggleWatchlistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tmdb_id' => ['required', 'integer', 'min:1'],
            'title' => ['required', 'string', 'max:255'],
            'year' => ['nullable', 'integer', 'min:1888', 'max:2100'],
            'poster' => ['nullable', 'string', 'max:255'],
            'rating' => ['nullable', 'string', 'max:20'],
            'votes' => ['nullable', 'string', 'max:50'],
            'primaryGenre' => ['nullable', 'string', 'max:100'],
            'overview' => ['nullable', 'string', 'max:5000'],
            'href' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array{
     *     tmdb_id: int,
     *     title: string,
     *     year?: int|null,
     *     poster?: string|null,
     *     rating?: string|null,
     *     votes?: string|null,
     *     primaryGenre?: string|null,
     *     overview?: string|null,
     *     href?: string|null
     * }
     */
    public function movieData(): array
    {
        /** @var array{
         *     tmdb_id: int,
         *     title: string,
         *     year?: int|null,
         *     poster?: string|null,
         *     rating?: string|null,
         *     votes?: string|null,
         *     primaryGenre?: string|null,
         *     overview?: string|null,
         *     href?: string|null
         * } $validated
         */
        $validated = $this->validated();

        return $validated;
    }
}
