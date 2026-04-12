<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Restoration extends Model
{
    protected $table = 'restorations';

    protected $fillable = [
        'item_id',
        'restored_at',
        'restoration_cost',
        'image_path',
    ];

    protected function casts(): array
    {
        return [
            'restored_at' => 'date',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isTechnicalSupport()) {
            return $query;
        }

        return $query->whereHas('item', fn (Builder $items) => $items->visibleTo($user));
    }
}
