<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\CityResource\Pages;
use App\Filament\Resources\CityResource\RelationManagers;
use App\Models\City;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Concerns\Resource\Gate;
use Illuminate\Support\Facades\Auth;
use Hexters\HexaLite\HasHexaLite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;

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
                                    ->options(\App\Models\Country::all()->pluck('name', 'id'))
                                    ->reactive()
                                    ->afterStateHydrated(function (callable $set, $record) {
                                        if ($record?->province) {
                                            $set('country_id', $record->province->country_id);
                                        }
                                    })
                                    ->afterStateUpdated(fn (callable $set) => $set('province_id', null))
                                    ->required(),
                                Forms\Components\Select::make('province_id')
                                    ->label('Province')
                                    ->options(function (callable $get) {
                                        $countryId = $get('country_id');
                                        return \App\Models\Province::where('country_id', $countryId)->pluck('name', 'id');
                                    })
                                    ->required()
                                    ->reactive()
                                    ->disabled(fn (callable $get) => empty($get('country_id')))
                                    ->afterStateHydrated(function (callable $set, $record) {
                                        $set('province_id', $record?->province_id);
                                    }),
                                Forms\Components\TextInput::make('name')   
                                    ->required(),
                            ]),
                        ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $withoutGlobalScope = ! Auth::user()?->can(static::getModelLabel() . '.withoutGlobalScope');

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('province.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('province.country.name')
                    ->searchable()
                    ->sortable(),  
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    } 

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'edit' => Pages\EditCity::route('/{record}/edit'),
        ];
    }
}
