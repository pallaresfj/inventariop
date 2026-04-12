<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Priest extends Model
{
    protected $fillable = [
        'name',
        'priest_title_id',
        'bio',
        'phone',
        'email',
        'image_path',
    ];

    public function priestTitle(): BelongsTo
    {
        return $this->belongsTo(PriestTitle::class);
    }

    public function parishAssignments(): HasMany
    {
        return $this->hasMany(ParishPriestAssignment::class);
    }

    public function deaneriesAsArchpriest(): HasMany
    {
        return $this->hasMany(Deanery::class, 'archpriest_id');
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
                'parishAssignments.parish',
                fn (Builder $parishes) => $parishes->where('deanery_id', $user->deanery_id)
            );
        }

        if ($user->isParishManager()) {
            return $user->parish_id
                ? $query->whereHas(
                    'parishAssignments',
                    fn (Builder $assignments) => $assignments->where('parish_id', $user->parish_id)
                )
                : $query->whereRaw('1 = 0');
        }

        if ($user->isCommunityManager()) {
            if (! $user->parish_id) {
                return $query->whereRaw('1 = 0');
            }

            return $query->whereHas(
                'parishAssignments',
                fn (Builder $assignments) => $assignments->where('parish_id', $user->parish_id)
            );
        }

        return $query->whereRaw('1 = 0');
    }
}
