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
    'deanery_id',
    'parish_id',
    'community_id',
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

    public function deanery(): BelongsTo
    {
        return $this->belongsTo(Deanery::class);
    }

    public function parish(): BelongsTo
    {
        return $this->belongsTo(Parish::class);
    }

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    public function isTechnicalSupport(): bool
    {
        return $this->hasRole('technical_support');
    }

    public function isDioceseManager(): bool
    {
        return $this->hasRole('diocese_manager');
    }

    public function isParishManager(): bool
    {
        return $this->hasRole('parish_manager');
    }

    public function isCommunityManager(): bool
    {
        return $this->hasRole('community_manager');
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
