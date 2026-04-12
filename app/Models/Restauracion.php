<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Restauracion extends Model
{
    protected $table = 'restauraciones';

    protected $fillable = [
        'articulo_id',
        'fecha_restauracion',
        'costo_restauracion',
        'imagen_path',
    ];

    protected function casts(): array
    {
        return [
            'fecha_restauracion' => 'date',
        ];
    }

    public function articulo(): BelongsTo
    {
        return $this->belongsTo(Articulo::class);
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isSoporteTecnico()) {
            return $query;
        }

        return $query->whereHas('articulo', fn (Builder $articulos) => $articulos->visibleTo($user));
    }
}
