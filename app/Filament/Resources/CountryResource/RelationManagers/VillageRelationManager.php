<?php

declare(strict_types = 1);

namespace App\Filament\Resources\CountryResource\RelationManagers;

use App\Models\City;
use App\Models\District;
use App\Models\Province;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class VillageRelationManager extends RelationManager
{
    protected static string $relationship = 'villages';

    /**
     * Configure the table for the villages relation manager.
     *
     * Defines the columns, filters, and actions for the villages table.
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
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
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Build the form for creating or editing a resource.
     *
     * Defines the form schema for creating/editing villages.
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('province_id')
                    ->label('Province')
                    ->options(function (RelationManager $livewire) {
                        $country = $livewire->getOwnerRecord();

                        if ($country == null) {
                            return [];
                        }

                        return Province::query()->where('country_id', $country->id)
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->reactive()
                    ->afterStateHydrated(function (callable $set, $record) {
                        if ($record?->district) {
                            $set('province_id', $record->district->city->province_id);
                        }
                    })
                    ->afterStateUpdated(function (callable $set) {
                        $set('city_id', null);
                    }),
                Forms\Components\Select::make('city_id')
                    ->label('City')
                    ->options(function (callable $get) {
                        $provinceId = $get('province_id');

                        if (! $provinceId) {
                            return [];
                        }

                        return City::query()->where('province_id', $provinceId)
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->reactive()
                    ->disabled(fn (callable $get): bool => empty($get('province_id')))
                    ->afterStateHydrated(function (callable $set, $record) {
                        if ($record?->district) {
                            $set('city_id', $record->district->city_id);
                        }
                    })
                    ->afterStateUpdated(fn (callable $set) => $set('district_id', null)),
                Forms\Components\Select::make('district_id')
                    ->label('District')
                    ->options(function (callable $get) {
                        $cityId = $get('city_id');

                        if (! $cityId) {
                            return [];
                        }

                        return District::query()->where('city_id', $cityId)
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->reactive()
                    ->disabled(fn (callable $get): bool => empty($get('city_id')))
                    ->afterStateHydrated(function (callable $set, $record) {
                        if ($record?->district) {
                            $set('district_id', $record->district_id);
                        }
                    })
                    ->afterStateUpdated(fn (callable $set) => $set('name', null)),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('latitude')
                    ->label('Latitude')
                    ->numeric()
                    ->helperText('e.g. -6.200000'),
                Forms\Components\TextInput::make('longitude')
                    ->label('Longitude')
                    ->numeric()
                    ->helperText('e.g. 106.816666'),
            ]);
    }
}
