<?php

namespace App\Filament\Pages\Auth;

use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Notifications\Notification;
use Hexters\HexaLite\Models\HexaRole;

class Register extends BaseRegister
{
    public function register(): ?RegistrationResponse
    {
        $data = $this->form->getState();

        $user = $this->handleRegistration($data);

        $roleId = HexaRole::where('name', 'Vendor')->first()->id;;
        if ($roleId) {
            $user->roles()->syncWithoutDetaching([$roleId]);
        }


        Notification::make()
            ->title('Registration Successful')
            ->success()
            ->send();

        return $this->redirectRoute('filament.dashboard.auth.login', navigate: false);
    }
}