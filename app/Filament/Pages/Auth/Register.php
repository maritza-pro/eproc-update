<?php

declare(strict_types = 1);

namespace App\Filament\Pages\Auth;

use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Register as BaseRegister;
use Hexters\HexaLite\Models\HexaRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException as ValidationValidationException;

class Register extends BaseRegister
{
    /**
     * Registers a new user.
     *
     * Handles user registration, role assignment, and sends a success notification.
     */
    public function register(): ?RegistrationResponse
    {
        return DB::transaction(function (): ?RegistrationResponse {

            $data = $this->form->getState();
            $user = $this->handleRegistration($data);
            $roleId = HexaRole::query()->where('name', 'User')->value('id');

            throw_if(! $roleId, ValidationValidationException::withMessages(['Role' => 'User Role Not Found']));

            /** @var \App\Models\User $user */
            $user->roles()->syncWithoutDetaching([$roleId]);

            Notification::make()
                ->title('Registration Successful, please check your email to verify!')
                ->success()
                ->send();

            return $this->redirectRoute('filament.dashboard.auth.login', navigate: false);
        });
    }
}
