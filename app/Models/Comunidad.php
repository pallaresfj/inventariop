<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comunidad extends Model
{
    protected $table = 'comunidades';

    protected $fillable = [
        'parroquia_id',
        'nombre',
        'legacy_login',
        'correo',
        'descripcion',
        'direccion',
        'telefono',
        'imagen_path',
    ];

    public function parroquia(): BelongsTo
    {
        return $this->belongsTo(Parroquia::class);
    }

    public function articulos(): HasMany
    {
        return $this->hasMany(Articulo::class);
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
            return $user->comunidad_id ? $query->whereKey($user->comunidad_id) : $query->whereRaw('1 = 0');
        }

        return $query->whereRaw('1 = 0');
    }
}
