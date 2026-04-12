<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsignacionParroquiaSacerdote extends Model
{
    protected $table = 'asignacion_parroquia_sacerdotes';

    protected $fillable = [
        'parroquia_id',
        'sacerdote_id',
        'cargo_parroquial_id',
        'vigente',
    ];

    protected function casts(): array
    {
        return [
            'vigente' => 'boolean',
        ];
    }

    public function parroquia(): BelongsTo
    {
        return $this->belongsTo(Parroquia::class);
    }

    public function sacerdote(): BelongsTo
    {
        return $this->belongsTo(Sacerdote::class);
    }

    public function cargoParroquial(): BelongsTo
    {
        return $this->belongsTo(CargoParroquial::class);
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isSoporteTecnico()) {
            return $query;
        }

        if ($user->isGestorDiocesis()) {
            if (! $user->arciprestazgo_id) {
                return $query;
            }

            return $query->whereHas(
                'parroquia',
                fn (Builder $parroquias) => $parroquias->where('arciprestazgo_id', $user->arciprestazgo_id)
            );
        }

        if ($user->isGestorParroquia()) {
            return $user->parroquia_id
                ? $query->where('parroquia_id', $user->parroquia_id)
                : $query->whereRaw('1 = 0');
        }

        if ($user->isGestorComunidad()) {
            return $user->parroquia_id
                ? $query->where('parroquia_id', $user->parroquia_id)
                : $query->whereRaw('1 = 0');
        }

        return $query->whereRaw('1 = 0');
    }
}
