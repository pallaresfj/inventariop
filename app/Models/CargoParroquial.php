<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CargoParroquial extends Model
{
    protected $table = 'cargos_parroquiales';

    protected $fillable = [
        'descripcion',
    ];

    public function asignacionesParroquiaSacerdote(): HasMany
    {
        return $this->hasMany(AsignacionParroquiaSacerdote::class);
    }
}
