<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParishRole extends Model
{
    protected $table = 'parish_roles';

    protected $fillable = [
        'description',
    ];

    public function parishPriestAssignments(): HasMany
    {
        return $this->hasMany(ParishPriestAssignment::class);
    }
}
