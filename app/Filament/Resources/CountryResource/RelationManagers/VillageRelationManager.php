<?php

declare(strict_types=1);

namespace App\Filament\Resources\CountryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VillageRelationManager extends RelationManager
{
    protected static string $relationship = 'village';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('province_id')
                    ->label('Province')
                    ->options(function (RelationManager $livewire) {
                        $country = $livewire->getOwnerRecord(); 
                        if (!$country) {
                            return [];
                        }

                        return \App\Models\Province::where('country_id', $country->id)
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->reactive()
                    ->afterStateHydrated(function ($set, $record) {
                        $set('province_id', $record?->province_id);
                    })
                    ->afterStateUpdated(function (callable $set) {
                        $set('city_id', null);
                    }),

                Forms\Components\Select::make('city_id')
                    ->label('City')
                    ->options(function (callable $get) {
                        $provinceId = $get('province_id');
                        if (!$provinceId) {
                            return [];
                        }

                        return \App\Models\City::where('province_id', $provinceId)
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->reactive()
                    ->disabled(fn(callable $get): bool => empty($get('province_id')))
                    ->afterStateHydrated(fn($set, $record) => $set('city_id', $record?->city_id))
                    ->afterStateUpdated(fn(callable $set) => $set('district_id', null)),

                Forms\Components\Select::make('district_id')
                    ->label('District')
                    ->options(function (callable $get) {
                        $cityId = $get('city_id');
                        if (!$cityId) {
                            return [];
                        }

                        return \App\Models\District::where('city_id', $cityId)
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->reactive()
                    ->disabled(fn(callable $get): bool => empty($get('city_id')))
                    ->afterStateHydrated(fn($set, $record) => $set('district_id', $record?->district_id))
                    ->afterStateUpdated(fn(callable $set) => $set('name', null)),

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
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
}
