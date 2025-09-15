<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class BidItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'bidItems';

    protected static ?string $title = 'Bids';

    /**
     * Configure the table for the relation manager.
     *
     * Defines the columns and filters for displaying bid items.
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('bid.vendor.company_name'),
                Tables\Columns\TextColumn::make('offered_quantity'),
                Tables\Columns\TextColumn::make('unit_price'),
                Tables\Columns\TextColumn::make('created_at'),
            ])
            ->filters([

            ]);
    }
}
