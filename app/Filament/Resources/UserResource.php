<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Concerns\Resource\Gate;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Facades\Filament;
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('roles.name'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('verifyEmail')
                    ->label('Verify Email')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (User $record): bool => is_null($record->email_verified_at))
                    // @phpstan-ignore method.notFound
                    ->authorize(fn () => Auth::user()?->roles()->doesntExist() ?? false)
                    ->action(function (User $record) {
                        $record->email_verified_at = now();
                        $record->save();
                        Notification::make()
                            ->title('Email verified.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                ActivityLogTimelineTableAction::make('Activities'),
                Tables\Actions\Action::make('resend_verification_email')
                    ->label('Resend Verification Email')
                    ->icon('heroicon-o-envelope')
                    ->authorize(fn (User $record): bool => ! $record->hasVerifiedEmail())
                    ->action(function (User $record) {
                        $notification = new VerifyEmail;
                        $notification->url = filament()->getVerifyEmailUrl($record);

                        $record->notify($notification);

                        Notification::make()
                            ->title('Verification email has been resent.')
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
        $withoutGlobalScope = ! Auth::user()?->can(static::getModelLabel() . '.withoutGlobalScope');

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
                                    ->disabled($withoutGlobalScope),
                                Forms\Components\TextInput::make('password')
                                    ->password()
                                    ->revealable()
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->nullable()
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->hidden($withoutGlobalScope),
                                Forms\Components\Select::make('roles')
                                    ->disabled($withoutGlobalScope)
                                    ->label('Role Name')
                                    ->relationship('roles', 'name')
                                    ->placeholder('Superuser'),
                                Forms\Components\Section::make('Change Password')
                                    ->collapsible()
                                    ->collapsed()
                                    ->hidden(fn (string $context): bool => $context === 'view' || ! $withoutGlobalScope)
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
                                            ->dehydrated(false),

                                        Forms\Components\TextInput::make('new_password_confirmation')
                                            ->label('Confirm New Password')
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
