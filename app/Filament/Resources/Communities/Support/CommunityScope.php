<?php

namespace App\Filament\Resources\Communities\Support;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CommunityScope
{
    public static function currentUser(): ?User
    {
        $user = Auth::user();

        return $user instanceof User ? $user : null;
    }

    public static function shouldLockParish(?User $user = null): bool
    {
        $user ??= self::currentUser();

        return (bool) ($user?->isParishManager() && $user->parish_id);
    }

    public static function resolveScopedParishId(?User $user = null): ?int
    {
        $user ??= self::currentUser();

        return $user?->parish_id ? (int) $user->parish_id : null;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function applyScopedValues(array $data, ?User $user = null): array
    {
        $user ??= self::currentUser();

        if (! $user?->isParishManager()) {
            return $data;
        }

        $parishId = self::resolveScopedParishId($user);

        if ($parishId) {
            $data['parish_id'] = $parishId;
        }

        return $data;
    }
}
