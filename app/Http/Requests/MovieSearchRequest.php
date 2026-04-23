<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class MovieSearchRequest extends FormRequest
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
            'search' => ['nullable', 'string', 'min:2', 'max:100'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $search = Str::of((string) $this->input('search'))
            ->trim()
            ->value();

        $this->merge([
            'search' => $search === '' ? null : $search,
        ]);
    }

    public function search(): ?string
    {
        /** @var ?string $search */
        $search = $this->validated('search');

        return $search;
    }
}
