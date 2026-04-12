<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Community extends Model
{
    protected $table = 'communities';

    protected $fillable = [
        'parish_id',
        'name',
        'legacy_login',
        'email',
        'description',
        'address',
        'phone',
        'image_path',
    ];

    public function parish(): BelongsTo
    {
        return $this->belongsTo(Parish::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isTechnicalSupport()) {
            return $query;
        }

        if ($user->isDioceseManager()) {
            if (! $user->deanery_id) {
                return $query;
            }

            return $query->whereHas(
                'parish',
                fn (Builder $parishes) => $parishes->where('deanery_id', $user->deanery_id)
            );
        }

        if ($user->isParishManager()) {
            return $user->parish_id
                ? $query->where('parish_id', $user->parish_id)
                : $query->whereRaw('1 = 0');
        }

        if ($user->isCommunityManager()) {
            return $user->community_id ? $query->whereKey($user->community_id) : $query->whereRaw('1 = 0');
        }

        return $query->whereRaw('1 = 0');
    }
}
