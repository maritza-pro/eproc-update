<?php

namespace App\Filament\Pages\Auth;

use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Register as BaseRegister;
use Hexters\HexaLite\Models\HexaRole;

class Register extends BaseRegister
{
    public function register(): ?RegistrationResponse
    {
        $data = $this->form->getState();

        $user = $this->handleRegistration($data);

        // TODO : ini bisa pake ->value('id') nanti coba diskus sama @kangmaup
        $roleId = HexaRole::where('name', 'Vendor')->first()->id;

        if ($roleId) {
            // TODO : disini coba makesure harus pake null safety atau engga
            $user->roles()->syncWithoutDetaching([$roleId]);
        }

        Notification::make()
            ->title('Registration Successful')
            ->success()
            ->send();

        return $this->redirectRoute('filament.dashboard.auth.login', navigate: false);
    }
}
