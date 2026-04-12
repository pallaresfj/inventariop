<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Arciprestazgo extends Model
{
    protected $fillable = [
        'nombre',
        'correo',
        'descripcion',
        'imagen_path',
        'arcipestre_id',
    ];

    public function parroquias(): HasMany
    {
        return $this->hasMany(Parroquia::class);
    }

    public function arcipestre(): BelongsTo
    {
        return $this->belongsTo(Sacerdote::class, 'arcipestre_id');
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isSoporteTecnico()) {
            return $query;
        }

        if ($user->isGestorDiocesis()) {
            return $user->arciprestazgo_id ? $query->whereKey($user->arciprestazgo_id) : $query;
        }

        if ($user->isGestorParroquia() && $user->parroquia_id) {
            return $query->whereHas('parroquias', fn (Builder $parroquias) => $parroquias->whereKey($user->parroquia_id));
        }

        if ($user->isGestorComunidad()) {
            if ($user->parroquia_id) {
                return $query->whereHas('parroquias', fn (Builder $parroquias) => $parroquias->whereKey($user->parroquia_id));
            }

            if ($user->comunidad_id) {
                return $query->whereHas(
                    'parroquias.comunidades',
                    fn (Builder $comunidades) => $comunidades->whereKey($user->comunidad_id)
                );
            }
        }

        return $query->whereRaw('1 = 0');
    }
}
