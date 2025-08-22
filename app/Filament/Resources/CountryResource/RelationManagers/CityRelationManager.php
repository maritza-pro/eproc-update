<?php

declare(strict_types = 1);

namespace App\Filament\Resources\CountryResource\RelationManagers;

use App\Models\Province;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CityRelationManager extends RelationManager
{
    protected static string $relationship = 'cities';

    /**
     * Configure the table for the relation manager.
     *
     * Defines the columns, filters, and actions for the cities table.
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
     * Build the form for creating or editing a record.
     *
     * Defines the form schema for managing city data.
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('province_id')
                    ->label('Province')
                    ->options(
                        fn (RelationManager $livewire) => Province::query()->where('country_id', $livewire->getOwnerRecord()->id)
                            ->pluck('name', 'id')
                    )
                    ->required()
                    ->reactive()
                    ->afterStateHydrated(fn ($set, $record) => $set('province_id', $record?->province_id)),
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
