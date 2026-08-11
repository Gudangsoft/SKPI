<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('avatar_path')
                    ->label('Foto Profil')
                    ->avatar()
                    ->imageEditor()
                    ->circleCropper()
                    ->disk('public')
                    ->directory('avatars')
                    ->visibility('public')
                    ->maxSize(1024)
                    ->helperText('Disarankan foto persegi minimal 400×400px. Maksimal 1 MB.'),
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                $this->getCurrentPasswordFormComponent(),
            ]);
    }
}
