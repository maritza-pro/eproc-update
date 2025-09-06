<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Concerns\Resource\Gate;
use App\Filament\Resources\VillageResource\Pages;
use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\Province;
use App\Models\Village;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Hexters\HexaLite\HasHexaLite;
use Illuminate\Support\Facades\Auth;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;

class VillageResource extends Resource
{
    use Gate {
        Gate::defineGates insteadof HasHexaLite;
    }
    use HasHexaLite;

    protected static ?string $model = Village::class;

    protected static ?string $modelLabel = 'Village';

    protected static ?string $navigationGroup = 'Location';

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?int $navigationSort = 5;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVillages::route('/'),
            'create' => Pages\CreateVillage::route('/create'),
            'edit' => Pages\EditVillage::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function table(Table $table): Table
    {
        // $withoutGlobalScope = ! Auth::user()?->can(static::getModelLabel() . '.withoutGlobalScope');

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('district.city.province.country.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('district.city.province.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('district.city.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('district.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label((string) __('Village'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('latitude')
                    ->label((string) __('Latitude')),
                Tables\Columns\TextColumn::make('longitude')
                    ->label((string) __('Longitude')),
            ])
            ->filters([
                //
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
                                        $set('district_id', null);
                                    })
                                    ->afterStateHydrated(function (callable $set, $record) {
                                        if ($record?->district?->city?->province) {
                                            $set('country_id', $record->district->city->province->country_id);
                                        }
                                    }),
                                Forms\Components\Select::make('province_id')
                                    ->label((string) __('Province'))
                                    ->reactive()
                                    ->required()
                                    ->disabled(fn (callable $get): bool => empty($get('country_id')))
                                    ->afterStateUpdated(function (callable $set) {
                                        $set('city_id', null);
                                        $set('district_id', null);
                                    })
                                    ->options(
                                        fn (callable $get) => $get('country_id')
                                            ? Province::query()->where('country_id', $get('country_id'))->pluck('name', 'id')
                                            : []
                                    )
                                    ->afterStateHydrated(function (callable $set, $record) {
                                        if ($record?->district?->city) {
                                            $set('province_id', $record->district->city->province_id);
                                        }
                                    }),
                                Forms\Components\Select::make('city_id')
                                    ->label((string) __('City'))
                                    ->reactive()
                                    ->required()
                                    ->disabled(fn (callable $get): bool => empty($get('province_id')))
                                    ->afterStateUpdated(fn (callable $set): mixed => $set('district_id', null))
                                    ->options(
                                        fn (callable $get) => $get('province_id')
                                            ? City::query()->where('province_id', $get('province_id'))->pluck('name', 'id')
                                            : []
                                    )
                                    ->afterStateHydrated(function (callable $set, $record) {
                                        if ($record && $record->district) {
                                            $set('city_id', $record->district->city_id);
                                        }
                                    }),
                                Forms\Components\Select::make('district_id')
                                    ->label((string) __('District'))
                                    ->reactive()
                                    ->required()
                                    ->disabled(fn (callable $get): bool => empty($get('city_id')))
                                    ->options(
                                        fn (callable $get) => $get('city_id')
                                            ? District::query()->where('city_id', $get('city_id'))->pluck('name', 'id')
                                            : []
                                    )
                                    ->afterStateHydrated(function (callable $set, $record) {
                                        if ($record?->district) {
                                            $set('district_id', $record->district_id);
                                        }
                                    }),
                                Forms\Components\TextInput::make('name')
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
