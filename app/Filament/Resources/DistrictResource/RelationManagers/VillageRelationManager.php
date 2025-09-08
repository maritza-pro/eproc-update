<?php

declare(strict_types = 1);

namespace App\Filament\Resources\DistrictResource\RelationManagers;

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
     * Defines the table columns, filters, and actions for villages.
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
     * Defines the form schema for managing village data.
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
