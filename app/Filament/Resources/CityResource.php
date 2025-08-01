<?php

declare(strict_types=1);

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
use Illuminate\Support\Facades\Auth;
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('country_id')
                                    ->label('Country')
                                    ->required()
                                    ->reactive()
                                    ->options(Country::all()->pluck('name', 'id'))
                                    ->afterStateUpdated(fn(callable $set) => $set('province_id', null))
                                    ->afterStateHydrated(function (callable $set, $record) {
                                        if ($record?->province) {
                                            $set('country_id', $record->province->country_id);
                                        }
                                    }),
                                Forms\Components\Select::make('province_id')
                                    ->label('Province')
                                    ->required()
                                    ->reactive()
                                    ->disabled(fn(callable $get): bool => empty($get('country_id')))
                                    ->afterStateHydrated(function (callable $set, $record) {
                                        $set('province_id', $record?->province_id);
                                    })
                                    ->options(
                                        fn(callable $get) =>
                                        $get('country_id')
                                            ? Province::where('country_id', $get('country_id'))->pluck('name', 'id')
                                            : []
                                    ),
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                                Forms\Components\TextInput::make('latitude')
                                    ->label('Latitude')
                                    ->numeric()
                                    ->helperText('e.g. -6.200000'),
                                Forms\Components\TextInput::make('longitude')
                                    ->label('Longitude')
                                    ->numeric()
                                    ->helperText('e.g. 106.816666'),
                            ]),
                    ]),
            ]);
    }

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
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('province.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('City')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('latitude')
                    ->label('Latitude'),
                Tables\Columns\TextColumn::make('longitude')
                    ->label('Longitude'),
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
}
