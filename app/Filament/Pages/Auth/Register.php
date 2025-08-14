<?php

namespace App\Filament\Pages\Auth;

use Dotenv\Exception\ValidationException;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Register as BaseRegister;
use Hexters\HexaLite\Models\HexaRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException as ValidationValidationException;

class Register extends BaseRegister
{
    public function register(): ?RegistrationResponse
    {
        return DB::transaction(function (): ?RegistrationResponse {
            
            $data = $this->form->getState();
    
            $user = $this->handleRegistration($data);
    
            // TODO : ini bisa pake ->value('id') nanti coba diskus sama @kangmaup
            $roleId = HexaRole::where('name', 'User')->value('id');
            
            throw_if(!$roleId, ValidationValidationException::withMessages(['Role' => 'Vendor Role Not Found']));
    
            $user->roles()->syncWithoutDetaching([$roleId]);
    
            Notification::make()
                ->title('Registration Successful, please check your email to verify!')
                ->success()
                ->send();
            return $this->redirectRoute('filament.dashboard.auth.login', navigate: false);
        });
    }
}
