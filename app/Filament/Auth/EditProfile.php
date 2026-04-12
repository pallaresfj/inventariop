<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Facades\Filament;

class EditProfile extends BaseEditProfile
{
    protected bool $passwordWasChanged = false;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = parent::mutateFormDataBeforeSave($data);

        $this->passwordWasChanged = array_key_exists('password', $data) && filled($data['password']);

        if ($this->passwordWasChanged) {
            $data['force_password_reset'] = false;
            $data['legacy_password_md5'] = null;
        }

        return $data;
    }

    protected function getRedirectUrl(): ?string
    {
        if (! $this->passwordWasChanged) {
            return parent::getRedirectUrl();
        }

        return Filament::getCurrentPanel()?->getUrl();
    }
}
