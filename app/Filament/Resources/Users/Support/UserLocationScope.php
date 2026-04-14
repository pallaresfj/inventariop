<?php

namespace App\Filament\Resources\Users\Support;

use Illuminate\Database\Eloquent\Builder;

class UserLocationScope
{
    public static function resolveSelectedDeaneryId(mixed $deaneryId): ?int
    {
        return self::normalizeId($deaneryId);
    }

    public static function resolveSelectedParishId(mixed $parishId): ?int
    {
        return self::normalizeId($parishId);
    }

    public static function shouldResetParishAndCommunity(mixed $newDeaneryId, mixed $oldDeaneryId): bool
    {
        return (string) ($newDeaneryId ?? '') !== (string) ($oldDeaneryId ?? '');
    }

    public static function shouldResetCommunity(mixed $newParishId, mixed $oldParishId): bool
    {
        return (string) ($newParishId ?? '') !== (string) ($oldParishId ?? '');
    }

    public static function scopeParishOptionsQuery(Builder $query, ?int $deaneryId): Builder
    {
        if (! $deaneryId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('deanery_id', $deaneryId);
    }

    public static function scopeCommunityOptionsQuery(Builder $query, ?int $parishId): Builder
    {
        if (! $parishId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('parish_id', $parishId);
    }

    private static function normalizeId(mixed $id): ?int
    {
        if (! is_numeric($id)) {
            return null;
        }

        $resolvedId = (int) $id;

        return $resolvedId > 0 ? $resolvedId : null;
    }
}
