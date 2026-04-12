<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sacerdote extends Model
{
    protected $fillable = [
        'nombre',
        'titulo_sacerdotal_id',
        'curriculo',
        'telefono',
        'correo',
        'imagen_path',
    ];

    public function tituloSacerdotal(): BelongsTo
    {
        return $this->belongsTo(TituloSacerdotal::class);
    }

    public function asignacionesParroquia(): HasMany
    {
        return $this->hasMany(AsignacionParroquiaSacerdote::class);
    }

    public function arciprestazgosComoArcipestre(): HasMany
    {
        return $this->hasMany(Arciprestazgo::class, 'arcipestre_id');
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
                'asignacionesParroquia.parroquia',
                fn (Builder $parroquias) => $parroquias->where('arciprestazgo_id', $user->arciprestazgo_id)
            );
        }

        if ($user->isGestorParroquia()) {
            return $user->parroquia_id
                ? $query->whereHas(
                    'asignacionesParroquia',
                    fn (Builder $asignaciones) => $asignaciones->where('parroquia_id', $user->parroquia_id)
                )
                : $query->whereRaw('1 = 0');
        }

        if ($user->isGestorComunidad()) {
            if (! $user->parroquia_id) {
                return $query->whereRaw('1 = 0');
            }

            return $query->whereHas(
                'asignacionesParroquia',
                fn (Builder $asignaciones) => $asignaciones->where('parroquia_id', $user->parroquia_id)
            );
        }

        return $query->whereRaw('1 = 0');
    }
}
