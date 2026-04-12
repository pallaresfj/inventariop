<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deanery extends Model
{
    protected $fillable = [
        'name',
        'email',
        'description',
        'image_path',
        'archpriest_id',
    ];

    public function parishes(): HasMany
    {
        return $this->hasMany(Parish::class);
    }

    public function archpriest(): BelongsTo
    {
        return $this->belongsTo(Priest::class, 'archpriest_id');
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isTechnicalSupport()) {
            return $query;
        }

        if ($user->isDioceseManager()) {
            return $user->deanery_id ? $query->whereKey($user->deanery_id) : $query;
        }

        if ($user->isParishManager() && $user->parish_id) {
            return $query->whereHas('parishes', fn (Builder $parishes) => $parishes->whereKey($user->parish_id));
        }

        if ($user->isCommunityManager()) {
            if ($user->parish_id) {
                return $query->whereHas('parishes', fn (Builder $parishes) => $parishes->whereKey($user->parish_id));
            }

            if ($user->community_id) {
                return $query->whereHas(
                    'parishes.communities',
                    fn (Builder $communities) => $communities->whereKey($user->community_id)
                );
            }
        }

        return $query->whereRaw('1 = 0');
    }
}
