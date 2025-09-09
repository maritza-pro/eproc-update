<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Concerns\Resource\Gate;
use App\Filament\Resources\CityResource\Pages;
use App\Filament\Resources\CityResource\RelationManagers\DistrictRelationManager;
use App\Filament\Resources\CityResource\RelationManagers\VillageRelationManager;
use App\Models\City;
use App\Models\Country;
use App\Models\Province;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Hexters\HexaLite\HasHexaLite;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;

class CityResource extends Resource
{
    use Gate {
        Gate::defineGates insteadof HasHexaLite;
    }
    use HasHexaLite;

    protected static ?string $model = City::class;

    protected static ?string $modelLabel = 'City';

    protected static ?string $navigationGroup = 'Location';

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?int $navigationSort = 3;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'edit' => Pages\EditCity::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            DistrictRelationManager::class,
            VillageRelationManager::class,
        ];
    }

    public static function table(Table $table): Table
    {
        // $withoutGlobalScope = ! Auth::user()?->can(static::getModelLabel() . '.withoutGlobalScope');

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('province.country.name')
                    ->label((string) __('Country'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('province.name')
                    ->label((string) __('Province'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label((string) __('City'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('latitude')
                    ->label((string) __('Latitude')),
                Tables\Columns\TextColumn::make('longitude')
                    ->label((string) __('Longitude')),
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                ActivityLogTimelineTableAction::make('Activities'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('country_id')
                                    ->label((string) __('Country'))
                                    ->required()
                                    ->reactive()
                                    ->options(Country::query()->pluck('name', 'id'))
                                    ->afterStateUpdated(fn (callable $set) => $set('province_id', null))
                                    ->afterStateHydrated(function (callable $set, $record) {
                                        if ($record?->province) {
                                            $set('country_id', $record->province->country_id);
                                        }
                                    }),
                                Forms\Components\Select::make('province_id')
                                    ->label((string) __('Province'))
                                    ->required()
                                    ->reactive()
                                    ->disabled(fn (callable $get): bool => empty($get('country_id')))
                                    ->afterStateHydrated(function (callable $set, $record) {
                                        $set('province_id', $record?->province_id);
                                    })
                                    ->options(
                                        fn (callable $get) => $get('country_id')
                                            ? Province::query()->where('country_id', $get('country_id'))->pluck('name', 'id')
                                            : []
                                    ),
                                Forms\Components\TextInput::make('name')
                                    ->label((string) __('City'))
                                    ->required(),
                                Forms\Components\TextInput::make('latitude')
                                    ->label((string) __('Latitude'))
                                    ->numeric()
                                    ->helperText((string) __('e.g. -6.200000')),
                                Forms\Components\TextInput::make('longitude')
                                    ->label((string) __('Longitude'))
                                    ->numeric()
                                    ->helperText((string) __('e.g. 106.816666')),
                            ]),
                    ]),
            ]);
    }
}
