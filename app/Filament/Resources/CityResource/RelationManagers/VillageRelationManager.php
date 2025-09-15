<?php

declare(strict_types=1);

namespace App\Filament\Resources\CityResource\RelationManagers;

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
     * Defines the form schema for managing village records.
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Select::make('district_id')
                    ->label((string) __('District'))
                    ->options(fn (RelationManager $livewire) => District::query()->where('city_id', $livewire->getOwnerRecord()->id)
                        ->pluck('name', 'id'))
                    ->required()
                    ->reactive()
                    ->afterStateHydrated(fn ($set, $record) => $set('district_id', $record?->district_id)),
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
