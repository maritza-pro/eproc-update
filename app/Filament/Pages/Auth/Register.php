<?php

namespace App\Filament\Pages\Auth;

use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Auth\Events\Registered;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class Register extends BaseRegister
{
    public function register(): ?RegistrationResponse
    {
        $data = $this->form->getState();

        $user = $this->handleRegistration($data);

        event(new Registered($user));

        $roleId = DB::table('hexa_roles')->where('name', 'Vendor')->value('id');
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