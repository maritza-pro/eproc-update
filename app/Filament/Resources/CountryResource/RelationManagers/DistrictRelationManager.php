<?php

declare(strict_types = 1);

namespace App\Filament\Resources\CountryResource\RelationManagers;

use App\Models\City;
use App\Models\Province;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DistrictRelationManager extends RelationManager
{
    protected static string $relationship = 'districts';

    /**
     * Configure the table for the relation manager.
     *
     * Defines the columns and actions for the districts table.
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
                    ->label((string) __('Latitude')),
                Tables\Columns\TextColumn::make('longitude')
                    ->label((string) __('Longitude')),
            ])
            ->filters([

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
     * Build the form for creating or editing a record.
     *
     * Defines the form schema for managing districts.
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('province_id')
                    ->label((string) __('Province'))
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
                        if ($record?->city) {
                            $set('province_id', $record->city->province_id);
                        }
                    })
                    ->afterStateUpdated(function (callable $set) {
                        $set('city_id', null);
                    }),
                Forms\Components\Select::make('city_id')
                    ->label((string) __('City'))
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
                        if ($record?->city) {
                            $set('city_id', $record->city_id);
                        }
                    })
                    ->afterStateUpdated(fn (callable $set) => $set('name', null)),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('latitude')
                    ->label((string) __('Latitude'))
                    ->numeric()
                    ->helperText((string) __('e.g. -6.200000')),
                Forms\Components\TextInput::make('longitude')
                    ->label((string) __('Longitude'))
                    ->numeric()
                    ->helperText((string) __('e.g. 106.816666')),
            ]);
    }
}
