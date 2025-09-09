<?php

declare(strict_types = 1);

namespace App\Filament\Resources\ProvinceResource\RelationManagers;

use App\Models\City;
use App\Models\District;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class VillageRelationManager extends RelationManager
{
    protected static string $relationship = 'villages';

    /**
     * Configure the table for the relation manager.
     *
     * Defines the columns and actions for the villages table.
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
     * Build the form for creating or editing a resource.
     *
     * Defines the form schema for managing villages.
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('city_id')
                    ->label((string) __('City'))
                    ->options(function (RelationManager $livewire) {
                        $province = $livewire->getOwnerRecord();

                        if ($province === null) {
                            return [];
                        }

                        return City::query()->where('province_id', $province->id)
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->reactive()
                    ->afterStateHydrated(function (callable $set, $record) {
                        if ($record?->district) {
                            $set('city_id', $record->district->city_id);
                        }
                    })
                    ->afterStateUpdated(function (callable $set) {
                        $set('district_id', null);
                    }),
                Forms\Components\Select::make('district_id')
                    ->label((string) __('District'))
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
