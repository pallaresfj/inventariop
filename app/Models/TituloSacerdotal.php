<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TituloSacerdotal extends Model
{
    protected $table = 'titulos_sacerdotales';

    protected $fillable = [
        'titulo',
        'descripcion',
    ];

    public function sacerdotes(): HasMany
    {
        return $this->hasMany(Sacerdote::class);
    }
}
