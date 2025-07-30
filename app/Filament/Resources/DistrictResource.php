<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Concerns\Resource\Gate;
use App\Filament\Resources\DistrictResource\Pages;
use App\Models\District;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Hexters\HexaLite\HasHexaLite;
use Illuminate\Support\Facades\Auth;
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
                                    ->afterStateUpdated(fn (callable $set) => $set('province_id', null))
                                    ->required(),
                                Forms\Components\Select::make('province_id')
                                    ->label('Province')
                                    ->options(fn (callable $get) => \App\Models\Province::where('country_id', $get('country_id'))->pluck('name', 'id'))
                                    ->disabled(fn (callable $get): bool => empty($get('country_id')))
                                    ->reactive()
                                    ->required(),
                                Forms\Components\Select::make('city_id')
                                    ->label('City')
                                    ->options(fn (callable $get) => \App\Models\City::where('province_id', $get('province_id'))->pluck('name', 'id'))
                                    ->disabled(fn (callable $get): bool => empty($get('province_id')))
                                    ->reactive()
                                    ->required(),
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                            ]),
                    ]),
            ]);
    }

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
            //
        ];
    }

    public static function table(Table $table): Table
    {
        // $withoutGlobalScope = ! Auth::user()?->can(static::getModelLabel() . '.withoutGlobalScope');

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('city.province.country.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.province.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('District')
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
}
