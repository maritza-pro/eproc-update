<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Concerns\Resource\Gate;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Auth\VerifyEmail;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Hexters\HexaLite\HasHexaLite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;

class UserResource extends Resource
{
    use Gate {
        Gate::defineGates insteadof HasHexaLite;
    }
    use HasHexaLite;

    protected static ?string $model = User::class;

    protected static ?string $modelLabel = 'User';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            // ActivitylogRelationManager::class,
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            // ->deferLoading()
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label((string) __('Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label((string) __('Email Verified At'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label((string) __('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label((string) __('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label((string) __('Deleted At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label((string) __('Roles')),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('verifyEmail')
                    ->label((string) __('Verify Email'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (User $record): bool => $record->email_verified_at === null)
                    // @phpstan-ignore method.nonObject
                    ->authorize(fn () => Auth::user()->roles()->doesntExist() ?? false)
                    ->action(function (User $record) {
                        $record->email_verified_at = now();
                        $record->save();
                        Notification::make()
                            ->title((string) __('Email verified.'))
                            ->success()
                            ->send();
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                ActivityLogTimelineTableAction::make('Activities'),
                Tables\Actions\Action::make('resend_verification_email')
                    ->label((string) __('Resend Verification Email'))
                    ->icon('heroicon-o-envelope')
                    ->authorize(fn (User $record): bool => ! $record->hasVerifiedEmail())
                    ->action(function (User $record) {
                        $notification = new VerifyEmail;
                        $notification->url = filament()->getVerifyEmailUrl($record);

                        $record->notify($notification);

                        Notification::make()
                            ->title((string) __('Verification email has been resent.'))
                            ->send();
                    })
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function form(Form $form): Form
    {
        $withoutGlobalScope = Auth::user()?->can(static::getModelLabel() . '.withoutGlobalScope');

        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label((string) __('Name'))
                                    ->required(),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required(),
                                Forms\Components\DateTimePicker::make('email_verified_at')
                                    ->label((string) __('Email Verified At'))
                                    ->disabled(! $withoutGlobalScope),
                                Forms\Components\TextInput::make('password')
                                    ->label((string) __('Password'))
                                    ->password()
                                    ->revealable()
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->nullable()
                                    ->dehydrated(fn ($state): bool => filled($state))
                                    ->hidden(! $withoutGlobalScope),
                                Forms\Components\Select::make('roles')
                                    ->label((string) __('Roles'))
                                    ->disabled(! $withoutGlobalScope)
                                    ->label((string) __('Role Name'))
                                    ->relationship('roles', 'name')
                                    ->placeholder((string) __('Superuser')),
                                Forms\Components\Section::make((string) __('Change Password'))
                                    ->collapsible()
                                    ->collapsed()
                                    ->hidden(fn (string $context): bool => $context === 'view' || $withoutGlobalScope)
                                    ->schema([
                                        Forms\Components\TextInput::make('current_password')
                                            ->label((string) __('Current Password'))
                                            ->password()
                                            ->revealable()
                                            ->dehydrated(false)
                                            ->rules([
                                                'required_with:new_password',
                                                'current_password',
                                            ]),

                                        Forms\Components\TextInput::make('new_password')
                                            ->label((string) __('New Password'))
                                            ->password()
                                            ->revealable()
                                            ->minLength(8)
                                            ->rules(['nullable', 'different:current_password'])
                                            ->confirmed()
                                            ->dehydrated(false),

                                        Forms\Components\TextInput::make('new_password_confirmation')
                                            ->label((string) __('Confirm New Password'))
                                            ->password()
                                            ->revealable()
                                            ->dehydrated(false)
                                            ->rules(['required_with:new_password']),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function canEdit(Model $record): bool
    {
        if (Auth::user()->can(static::getModelLabel() . '.withoutGlobalScope') && Auth::user()->can(static::getModelLabel() . '.edit')) {
            return true;
        }

        return Auth::user()->can(static::getModelLabel() . '.edit') && $record->id == Auth::id();
    }

    public static function canView(Model $record): bool
    {
        if (Auth::user()->can(static::getModelLabel() . '.withoutGlobalScope') && Auth::user()->can(static::getModelLabel() . '.view')) {
            return true;
        }

        return Auth::user()->can(static::getModelLabel() . '.view') && $record->id == Auth::id();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
