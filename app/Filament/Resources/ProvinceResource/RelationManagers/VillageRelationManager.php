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

    public function form(Form $form): Form
    {

        return $form
            ->schema([
                Forms\Components\Select::make('city_id')
                    ->label('City')
                    ->options(function (RelationManager $livewire) {
                        $province = $livewire->getOwnerRecord();

                        if (! $province) {
                            return [];
                        }

                        return City::where('province_id', $province->id)
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
                    ->label('District')
                    ->options(function (callable $get) {
                        $cityId = $get('city_id');

                        if (! $cityId) {
                            return [];
                        }

                        return District::where('city_id', $cityId)
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
