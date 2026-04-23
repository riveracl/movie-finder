<?php

namespace App\Services;

use RuntimeException;

class TmdbServiceException extends RuntimeException
{
    public static function missingCredentials(): self
    {
        return new self('TMDB credentials are missing. Please configure services.tmdb before searching for movies.');
    }

    public static function requestFailed(string $message, ?int $status = null): self
    {
        return new self($message, $status ?? 0);
    }
}
