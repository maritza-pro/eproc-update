<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Concerns\Resource\Gate;
use App\Filament\Resources\CountryResource\Pages;
use App\Filament\Resources\CountryResource\RelationManagers\CityRelationManager;
use App\Filament\Resources\CountryResource\RelationManagers\DistrictRelationManager;
use App\Filament\Resources\CountryResource\RelationManagers\ProvinceRelationManager;
use App\Filament\Resources\CountryResource\RelationManagers\VillageRelationManager;
use App\Models\Country;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Hexters\HexaLite\HasHexaLite;

class CountryResource extends Resource
{
    use Gate {
        Gate::defineGates insteadof HasHexaLite;
    }
    use HasHexaLite;

    protected static ?string $model = Country::class;

    protected static ?string $modelLabel = 'Country';

    protected static ?string $navigationGroup = 'Location';

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?int $navigationSort = 1;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCountries::route('/'),
            'create' => Pages\CreateCountry::route('/create'),
            'edit' => Pages\EditCountry::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            ProvinceRelationManager::class,
            CityRelationManager::class,
            DistrictRelationManager::class,
            VillageRelationManager::class,
        ];
    }

    public static function table(Table $table): Table
    {
        // $withoutGlobalScope = ! Auth::user()?->can(static::getModelLabel() . '.withoutGlobalScope');

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label((string) __('Country Name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->label((string) __('Country Code'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('iso')
                    ->label((string) __('ISO Code')),

                Tables\Columns\TextColumn::make('num_code')
                    ->label((string) __('Numeric Code')),

                Tables\Columns\TextColumn::make('currency')
                    ->label((string) __('Currency')),

                Tables\Columns\TextColumn::make('msisdn_code')
                    ->label((string) __('MSISDN Code')),

                Tables\Columns\TextColumn::make('latitude')
                    ->label((string) __('Latitude')),

                Tables\Columns\TextColumn::make('longitude')
                    ->label((string) __('Longitude')),
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
                                Forms\Components\TextInput::make('name')
                                    ->required(),

                                Forms\Components\TextInput::make('code')
                                    ->label((string) __('Country Code'))
                                    ->maxLength(2)
                                    ->helperText((string) __('2-letter country code (e.g. ID, US)'))
                                    ->extraAttributes(['style' => 'text-transform: uppercase']),

                                Forms\Components\TextInput::make('currency')
                                    ->label((string) __('Currency'))
                                    ->maxLength(10),

                                Forms\Components\TextInput::make('iso')
                                    ->label((string) __('ISO Code'))
                                    ->maxLength(3)
                                    ->helperText((string) __('3-letter ISO code (e.g. IDN, USA)')),

                                Forms\Components\TextInput::make('num_code')
                                    ->label((string) __('Numeric Code'))
                                    ->numeric()
                                    ->maxLength(3)
                                    ->helperText((string) __('Numeric country code (e.g. 360 for Indonesia)')),

                                Forms\Components\TextInput::make('msisdn_code')
                                    ->label((string) __('MSISDN Code'))
                                    ->numeric()
                                    ->maxLength(5)
                                    ->helperText((string) __('e.g. 62 for Indonesia')),

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
