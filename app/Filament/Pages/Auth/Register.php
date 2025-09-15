<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use Filament\Forms;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Register as BaseRegister;
use Hexters\HexaLite\Models\HexaRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException as ValidationValidationException;

class Register extends BaseRegister
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label((string) __('Business Name'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),
                        Forms\Components\TextInput::make('email')
                            ->label((string) __('Business Email'))
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique($this->getUserModel())
                            ->helperText((string) __('*registered email will be used as primary email')),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

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

            $user->vendor()->create([
                'company_name' => data_get($data, 'name'),
                'email' => data_get($data, 'email'),
            ]);

            Notification::make()
                ->title((string) __('Registration Successful, please check your email to verify!'))
                ->success()
                ->send();

            return $this->redirectRoute('filament.dashboard.auth.login', navigate: false);
        });
    }
}
