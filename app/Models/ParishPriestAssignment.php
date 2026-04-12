<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParishPriestAssignment extends Model
{
    protected $table = 'parish_priest_assignments';

    protected $fillable = [
        'parish_id',
        'priest_id',
        'parish_role_id',
        'is_current',
    ];

    protected function casts(): array
    {
        return [
            'is_current' => 'boolean',
        ];
    }

    public function parish(): BelongsTo
    {
        return $this->belongsTo(Parish::class);
    }

    public function priest(): BelongsTo
    {
        return $this->belongsTo(Priest::class);
    }

    public function parishRole(): BelongsTo
    {
        return $this->belongsTo(ParishRole::class);
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
            return $user->parish_id
                ? $query->where('parish_id', $user->parish_id)
                : $query->whereRaw('1 = 0');
        }

        return $query->whereRaw('1 = 0');
    }
}
