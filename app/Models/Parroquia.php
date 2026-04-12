<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Parroquia extends Model
{
    protected $fillable = [
        'arciprestazgo_id',
        'nombre',
        'legacy_login',
        'correo',
        'descripcion',
        'direccion',
        'telefono',
        'web',
        'imagen_path',
    ];

    public function arciprestazgo(): BelongsTo
    {
        return $this->belongsTo(Arciprestazgo::class);
    }

    public function comunidades(): HasMany
    {
        return $this->hasMany(Comunidad::class);
    }

    public function articulos(): HasMany
    {
        return $this->hasMany(Articulo::class);
    }

    public function asignacionesSacerdotales(): HasMany
    {
        return $this->hasMany(AsignacionParroquiaSacerdote::class);
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isSoporteTecnico()) {
            return $query;
        }

        if ($user->isGestorDiocesis()) {
            return $user->arciprestazgo_id ? $query->where('arciprestazgo_id', $user->arciprestazgo_id) : $query;
        }

        if ($user->isGestorParroquia()) {
            return $user->parroquia_id ? $query->whereKey($user->parroquia_id) : $query->whereRaw('1 = 0');
        }

        if ($user->isGestorComunidad()) {
            if ($user->parroquia_id) {
                return $query->whereKey($user->parroquia_id);
            }

            return $user->comunidad_id
                ? $query->whereHas('comunidades', fn (Builder $comunidades) => $comunidades->whereKey($user->comunidad_id))
                : $query->whereRaw('1 = 0');
        }

        return $query->whereRaw('1 = 0');
    }
}
