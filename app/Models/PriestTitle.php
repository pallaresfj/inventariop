<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PriestTitle extends Model
{
    protected $table = 'priest_titles';

    protected $fillable = [
        'title',
        'description',
    ];

    public function priests(): HasMany
    {
        return $this->hasMany(Priest::class);
    }
}
