<?php

namespace App\Filament\Resources\Items\Support;

use App\Models\Community;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ItemScope
{
    public static function currentUser(): ?User
    {
        $user = Auth::user();

        return $user instanceof User ? $user : null;
    }

    public static function shouldLockParish(?User $user = null): bool
    {
        $user ??= self::currentUser();

        return $user?->isParishManager() || $user?->isCommunityManager();
    }

    public static function shouldLockCommunity(?User $user = null): bool
    {
        $user ??= self::currentUser();

        return (bool) $user?->isCommunityManager();
    }

    public static function resolveScopedParishId(?User $user = null): ?int
    {
        $user ??= self::currentUser();

        if (! $user) {
            return null;
        }

        if ($user->parish_id) {
            return (int) $user->parish_id;
        }

        if ($user->community_id) {
            return (int) (Community::query()->whereKey($user->community_id)->value('parish_id') ?: 0) ?: null;
        }

        return null;
    }

    public static function resolveScopedCommunityId(?User $user = null): ?int
    {
        $user ??= self::currentUser();

        return $user?->community_id ? (int) $user->community_id : null;
    }

    public static function resolveSelectedParishId(mixed $parishId, ?User $user = null): ?int
    {
        $parishId = is_numeric($parishId) ? (int) $parishId : null;

        return $parishId ?: self::resolveScopedParishId($user);
    }

    public static function shouldResetCommunity(mixed $newParishId, mixed $oldParishId): bool
    {
        return (string) ($newParishId ?? '') !== (string) ($oldParishId ?? '');
    }

    public static function scopeCommunityOptionsQuery(Builder $query, ?int $parishId, ?User $user = null): Builder
    {
        if (! $parishId) {
            return $query->whereRaw('1 = 0');
        }

        $query->where('parish_id', $parishId);

        $user ??= self::currentUser();

        if ($user?->isCommunityManager() && $user->community_id) {
            $query->whereKey($user->community_id);
        }

        return $query;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function applyScopedValues(array $data, ?User $user = null): array
    {
        $user ??= self::currentUser();

        if (! $user) {
            return $data;
        }

        if ($user->isCommunityManager()) {
            $communityId = self::resolveScopedCommunityId($user);
            $parishId = self::resolveScopedParishId($user);

            if ($communityId) {
                $data['community_id'] = $communityId;
            }

            if ($parishId) {
                $data['parish_id'] = $parishId;
            }

            return $data;
        }

        if ($user->isParishManager()) {
            $parishId = self::resolveScopedParishId($user);

            if ($parishId) {
                $data['parish_id'] = $parishId;
            }
        }

        return $data;
    }
}
