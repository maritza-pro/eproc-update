<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Concerns\Resource\Gate;
use App\Filament\Resources\ProvinceResource\Pages;
use App\Filament\Resources\ProvinceResource\RelationManagers\CityRelationManager;
use App\Filament\Resources\ProvinceResource\RelationManagers\DistrictRelationManager;
use App\Filament\Resources\ProvinceResource\RelationManagers\VillageRelationManager;
use App\Models\Province;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Hexters\HexaLite\HasHexaLite;
use Illuminate\Support\Facades\Auth;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;

class ProvinceResource extends Resource
{
    use Gate {
        Gate::defineGates insteadof HasHexaLite;
    }
    use HasHexaLite;

    protected static ?string $model = Province::class;

    protected static ?string $modelLabel = 'Province';

    protected static ?string $navigationGroup = 'Location';

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('country_id')
                                    ->relationship('country', 'name')
                                    ->required(),
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
            'index' => Pages\ListProvinces::route('/'),
            'create' => Pages\CreateProvince::route('/create'),
            'edit' => Pages\EditProvince::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
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
                Tables\Columns\TextColumn::make('country.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Province')
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
