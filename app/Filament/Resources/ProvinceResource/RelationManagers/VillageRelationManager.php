<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProvinceResource\RelationManagers;

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
                Forms\Components\Select::make('city_id')
                    ->label('City')
                    ->options(function (RelationManager $livewire) {
                        $province = $livewire->getOwnerRecord();
                        if (!$province) return [];

                        return \App\Models\City::where('province_id', $province->id)
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->reactive()
                    ->afterStateHydrated(function ($set, $record) {
                        $set('city_id', $record?->city_id);
                    })
                    ->afterStateUpdated(function (callable $set) {
                        $set('district_id', null);
                    }),

                Forms\Components\Select::make('district_id')
                    ->label('District')
                    ->options(function (callable $get) {
                        $cityId = $get('city_id');
                        if (!$cityId) return [];

                        return \App\Models\District::where('city_id', $cityId)
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->reactive()
                    ->disabled(fn(callable $get) => empty($get('city_id')))
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
