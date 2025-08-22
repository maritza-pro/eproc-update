<?php

declare(strict_types = 1);

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'profile';

    protected static ?string $title = 'My Profile';

    protected static string $view = 'filament.pages.profile';

    public ?array $data = [];

    /**
     * Mount the form.
     *
     * Fills the form with the currently authenticated user's attributes.
     */
    public function mount(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $this->form->fill($user->getAttributes());

    }

    /**
     * Build the form for the page.
     *
     * Defines the form schema for user profile management.
     */
    public function form(Form $form): Form
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->readOnly()
                                    ->required(),
                                Forms\Components\DateTimePicker::make('email_verified_at')
                                    ->disabled(),
                                Forms\Components\Select::make('roles')
                                    ->disabled()
                                    ->label('Role Name')
                                    ->relationship('roles', 'name')
                                    ->placeholder('Superuser'),
                                Forms\Components\DateTimePicker::make('updated_at')
                                    ->label('Last Updated')
                                    ->disabled(),
                                Forms\Components\Section::make('Change Password')
                                    ->collapsible()
                                    ->collapsed()
                                    ->schema([
                                        Forms\Components\TextInput::make('current_password')
                                            ->label('Current Password')
                                            ->password()
                                            ->revealable()
                                            ->dehydrated(false)
                                            ->rules([
                                                'required_with:new_password',
                                                'current_password',
                                            ]),

                                        Forms\Components\TextInput::make('new_password')
                                            ->label('New Password')
                                            ->password()
                                            ->revealable()
                                            ->minLength(8)
                                            ->rules(['nullable', 'different:current_password'])
                                            ->confirmed()
                                            ->dehydrated(fn ($state) => filled($state)),

                                        Forms\Components\TextInput::make('new_password_confirmation')
                                            ->label('Confirm New Password')
                                            ->password()
                                            ->revealable()
                                            ->dehydrated(false)
                                            ->rules(['required_with:new_password']),
                                    ]),
                            ]),
                    ]),
            ])
            ->statePath('data')
            ->model($user);
    }

    /**
     * Saves the user's profile data.
     * Updates user details and password if provided.
     */
    public function save(): void
    {

        $data = $this->form->getState();

        DB::transaction(function () use ($data): void {

            /** @var \App\Models\User $user */
            $user = Auth::user();

            $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
            ]);

            if (filled($data['new_password'] ?? null)) {
                $user->update([
                    'password' => $data['new_password'],
                ]);

                $this->data['current_password'] = null;
                $this->data['new_password'] = null;
                $this->data['new_password_confirmation'] = null;
            }

            $this->form->fill($user->getAttributes());

            Notification::make()
                ->title('Profile updated successfully')
                ->success()
                ->send();
        });

    }
}
