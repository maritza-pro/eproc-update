<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Concerns\Resource\Gate;
use App\Filament\Resources\DistrictResource\Pages;
use App\Filament\Resources\DistrictResource\RelationManagers\VillageRelationManager;
use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\Province;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Hexters\HexaLite\HasHexaLite;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;

class DistrictResource extends Resource
{
    use Gate {
        Gate::defineGates insteadof HasHexaLite;
    }
    use HasHexaLite;

    protected static ?string $model = District::class;

    protected static ?string $modelLabel = 'District';

    protected static ?string $navigationGroup = 'Location';

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?int $navigationSort = 4;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDistricts::route('/'),
            'create' => Pages\CreateDistrict::route('/create'),
            'edit' => Pages\EditDistrict::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            VillageRelationManager::class,
        ];
    }

    public static function table(Table $table): Table
    {
        // $withoutGlobalScope = ! Auth::user()?->can(static::getModelLabel() . '.withoutGlobalScope');

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('city.province.country.name')
                    ->label((string) __('Country'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.province.name')
                    ->label((string) __('Province'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label((string) __('City'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label((string) __('District'))
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
                                    ->reactive()
                                    ->required()
                                    ->options(Country::query()->pluck('name', 'id'))
                                    ->afterStateUpdated(function (callable $set) {
                                        $set('province_id', null);
                                        $set('city_id', null);
                                    })
                                    ->afterStateHydrated(function (callable $set, $record) {
                                        if ($record?->city?->province) {
                                            $set('country_id', $record->city->province->country_id);
                                        }
                                    }),
                                Forms\Components\Select::make('province_id')
                                    ->label((string) __('Province'))
                                    ->reactive()
                                    ->required()
                                    ->disabled(fn (callable $get): bool => empty($get('country_id')))
                                    ->afterStateUpdated(fn (callable $set): mixed => $set('city_id', null))
                                    ->options(
                                        fn (callable $get) => $get('country_id')
                                            ? Province::query()->where('country_id', $get('country_id'))->pluck('name', 'id')
                                            : []
                                    )
                                    ->afterStateHydrated(function (callable $set, $record) {
                                        if ($record?->city) {
                                            $set('province_id', $record->city->province_id);
                                        }
                                    }),
                                Forms\Components\Select::make('city_id')
                                    ->label((string) __('City'))
                                    ->reactive()
                                    ->required()
                                    ->disabled(fn (callable $get): bool => empty($get('province_id')))
                                    ->options(
                                        fn (callable $get) => $get('province_id')
                                            ? City::query()->where('province_id', $get('province_id'))->pluck('name', 'id')
                                            : []
                                    )
                                    ->afterStateHydrated(function (callable $set, $record) {
                                        if ($record?->city) {
                                            $set('city_id', $record->city_id);
                                        }
                                    }),
                                Forms\Components\TextInput::make('name')
                                    ->label((string) __('District'))
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
