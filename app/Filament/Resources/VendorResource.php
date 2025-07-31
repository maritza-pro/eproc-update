<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Concerns\Resource\Gate;
use App\Filament\Resources\VendorResource\Pages;
use App\Models\Vendor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Hexters\HexaLite\HasHexaLite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;
use App\Models\Bank;

class VendorResource extends Resource
{
    use Gate {
        Gate::defineGates insteadof HasHexaLite;
    }
    use HasHexaLite;

    protected static ?string $model = Vendor::class;

    protected static ?string $modelLabel = 'Vendor';

    protected static ?string $navigationGroup = 'Vendors';

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?int $navigationSort = 1;

    public static function canCreate(): bool
    {
        if (Auth::user()?->can(static::getModelLabel() . '.withoutGlobalScope')) {
            return true;
        }

        return Auth::user()?->can(static::getModelLabel() . '.create') && self::$model::query()->when(Auth::id(), fn (Builder $query): Builder => $query->where('user_id', Auth::id()))->count() < 1;
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
                                Forms\Components\TextInput::make('company_name')
                                    ->required(),
                                Forms\Components\Select::make('business_field_id')
                                    ->relationship('businessField', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->label('Business Field'),
								Forms\Components\Select::make('bank_id')
									->label('Informasi Bank')
									->relationship('bank', 'id')
									->getOptionLabelFromRecordUsing(fn (Bank $record) => "{$record->bank_name} - {$record->bank_account_number} (a.n {$record->bank_account_name})")
									->searchable()
									->preload()
									->required(),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required(),
                                Forms\Components\TextInput::make('phone')
                                    ->tel(),
                                Forms\Components\TextInput::make('tax_number'),
                                Forms\Components\TextInput::make('business_number'),
                                Forms\Components\TextInput::make('license_number'),
                                Forms\Components\Select::make('taxonomies')
                                    ->relationship('taxonomies', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->label('Vendor Type'),
                                Forms\Components\Toggle::make('is_verified')
                                    ->required()
                                    ->disabled($withoutGlobalScope),
                                Forms\Components\Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->required()
                                    ->searchable()
                                    ->default($withoutGlobalScope ? Auth::id() : null)
                                    ->disabled($withoutGlobalScope)
                                    ->dehydrated(),
                            ]),
                    ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVendors::route('/'),
            'create' => Pages\CreateVendor::route('/create'),
            'view' => Pages\ViewVendor::route('/{record}'),
            'edit' => Pages\EditVendor::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            ActivitylogRelationManager::class,
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            // ->deferLoading()
            ->striped()
            ->modifyQueryUsing(function (Builder $query) {
                $query->unless(Auth::user()?->can(static::getModelLabel() . '.withoutGlobalScope'), function (Builder $query) {
                    $query->where('user_id', Auth::id());
                });
            })
            ->columns([
                Tables\Columns\IconColumn::make('is_verified')
                    ->boolean()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('company_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('businessField.name')
                    ->searchable()
                    ->sortable(),
				Tables\Columns\TextColumn::make('bank.bank_name')
					->searchable()
					->sortable()
					->badge(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tax_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('business_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('license_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('taxonomies.name')
                    ->label('Vendor Type')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
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
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                ActivityLogTimelineTableAction::make('Activities'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }
}
