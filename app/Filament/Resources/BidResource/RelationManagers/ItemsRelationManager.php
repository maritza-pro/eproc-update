<?php

declare(strict_types = 1);

namespace App\Filament\Resources\BidResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    /**
     * Configure the table for the relation manager.
     *
     * Defines the columns and actions for the items table.
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('procurementItem.product.name')
                    ->label('Product'),

                Tables\Columns\TextColumn::make('offered_quantity')
                    ->sortable(),

                Tables\Columns\TextColumn::make('unit_price')
                    ->money('Rp.')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
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
     * Defines the form schema for managing bid items.
     */
    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('procurement_item_id')
                ->label('Procurement Item')
                ->options(\App\Models\ProcurementItem::with('product')->get()->pluck('product.name', 'id'))
                ->searchable()
                ->required(),

            Forms\Components\TextInput::make('offered_quantity')
                ->numeric()
                ->minValue(1)
                ->required(),

            Forms\Components\TextInput::make('unit_price')
                ->numeric()
                ->minValue(0)
                ->required(),

            Forms\Components\Textarea::make('notes')
                ->rows(2),
        ]);
    }
}
