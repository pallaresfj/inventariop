<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'username',
    'name',
    'email',
    'password',
    'is_active',
    'force_password_reset',
    'legacy_password_md5',
    'picture_path',
    'arciprestazgo_id',
    'parroquia_id',
    'comunidad_id',
])]
#[Hidden(['password', 'remember_token', 'legacy_password_md5'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use HasRoles;
    use Notifiable;

    protected static function booted(): void
    {
        static::saving(function (self $user): void {
            if ($user->exists && $user->isDirty('password')) {
                $user->legacy_password_md5 = null;
            }
        });
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active;
    }

    public function arciprestazgo(): BelongsTo
    {
        return $this->belongsTo(Arciprestazgo::class);
    }

    public function parroquia(): BelongsTo
    {
        return $this->belongsTo(Parroquia::class);
    }

    public function comunidad(): BelongsTo
    {
        return $this->belongsTo(Comunidad::class);
    }

    public function isSoporteTecnico(): bool
    {
        return $this->hasRole('soporte_tecnico');
    }

    public function isGestorDiocesis(): bool
    {
        return $this->hasRole('gestor_diocesis');
    }

    public function isGestorParroquia(): bool
    {
        return $this->hasRole('gestor_parroquia');
    }

    public function isGestorComunidad(): bool
    {
        return $this->hasRole('gestor_comunidad');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'force_password_reset' => 'boolean',
        ];
    }
}
