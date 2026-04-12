<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Parish extends Model
{
    protected $fillable = [
        'deanery_id',
        'name',
        'legacy_login',
        'email',
        'description',
        'address',
        'phone',
        'web',
        'image_path',
    ];

    public function deanery(): BelongsTo
    {
        return $this->belongsTo(Deanery::class);
    }

    public function communities(): HasMany
    {
        return $this->hasMany(Community::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function priestAssignments(): HasMany
    {
        return $this->hasMany(ParishPriestAssignment::class);
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isTechnicalSupport()) {
            return $query;
        }

        if ($user->isDioceseManager()) {
            return $user->deanery_id ? $query->where('deanery_id', $user->deanery_id) : $query;
        }

        if ($user->isParishManager()) {
            return $user->parish_id ? $query->whereKey($user->parish_id) : $query->whereRaw('1 = 0');
        }

        if ($user->isCommunityManager()) {
            if ($user->parish_id) {
                return $query->whereKey($user->parish_id);
            }

            return $user->community_id
                ? $query->whereHas('communities', fn (Builder $communities) => $communities->whereKey($user->community_id))
                : $query->whereRaw('1 = 0');
        }

        return $query->whereRaw('1 = 0');
    }
}
