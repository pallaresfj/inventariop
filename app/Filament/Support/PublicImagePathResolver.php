<?php

namespace App\Filament\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicImagePathResolver
{
    /**
     * @param  array<int, callable(string): string>  $pathTransforms
     */
    public static function resolveExistingState(?string $imagePath, array $pathTransforms = []): ?string
    {
        if (blank($imagePath)) {
            return null;
        }

        $normalizedPath = self::normalizePath($imagePath);

        if (blank($normalizedPath)) {
            return null;
        }

        if (filter_var($normalizedPath, FILTER_VALIDATE_URL)) {
            return $normalizedPath;
        }

        $candidatePaths = [$normalizedPath];

        foreach ($pathTransforms as $pathTransform) {
            $candidatePaths[] = $pathTransform($normalizedPath);
        }

        foreach (array_values(array_unique($candidatePaths)) as $candidatePath) {
            if ($candidatePath !== '' && Storage::disk('public')->exists($candidatePath)) {
                return $candidatePath;
            }
        }

        return null;
    }

    /**
     * @param  array<int, callable(string): string>  $pathTransforms
     */
    public static function resolveExistingUrl(?string $imagePath, array $pathTransforms = []): ?string
    {
        $resolvedState = self::resolveExistingState($imagePath, $pathTransforms);

        if (blank($resolvedState)) {
            return null;
        }

        if (filter_var($resolvedState, FILTER_VALIDATE_URL)) {
            return $resolvedState;
        }

        return Storage::disk('public')->url($resolvedState);
    }

    private static function normalizePath(string $imagePath): ?string
    {
        $normalizedPath = str_replace('\\', '/', trim($imagePath));
        $normalizedPath = preg_replace('/\0+$/', '', $normalizedPath) ?: $normalizedPath;

        if (filter_var($normalizedPath, FILTER_VALIDATE_URL)) {
            $urlPath = parse_url($normalizedPath, PHP_URL_PATH);

            if (is_string($urlPath) && str_contains($urlPath, '/storage/')) {
                $normalizedPath = Str::after($urlPath, '/storage/');
            } else {
                return $normalizedPath;
            }
        }

        if (str_contains($normalizedPath, '/storage/app/public/')) {
            $normalizedPath = Str::after($normalizedPath, '/storage/app/public/');
        }

        foreach (['storage/app/public/', 'public/storage/', 'storage/'] as $prefix) {
            if (str_starts_with($normalizedPath, $prefix)) {
                $normalizedPath = substr($normalizedPath, strlen($prefix));
                break;
            }
        }

        $normalizedPath = ltrim($normalizedPath, '/');

        return $normalizedPath === '' ? null : $normalizedPath;
    }
}
