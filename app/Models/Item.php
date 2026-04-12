<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $fillable = [
        'parish_id',
        'community_id',
        'name',
        'description',
        'image_path',
        'condition',
        'price',
        'acquired_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'acquired_at' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function parish(): BelongsTo
    {
        return $this->belongsTo(Parish::class);
    }

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    public function restorations(): HasMany
    {
        return $this->hasMany(Restoration::class);
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
            return $user->community_id
                ? $query->where('community_id', $user->community_id)
                : $query->whereRaw('1 = 0');
        }

        return $query->whereRaw('1 = 0');
    }
}
