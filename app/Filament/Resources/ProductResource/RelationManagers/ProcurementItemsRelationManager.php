<?php

declare(strict_types = 1);

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ProcurementItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'procurementItems';

    protected static ?string $title = 'Procurements';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('procurement.title')
            ->columns([
                Tables\Columns\TextColumn::make('procurement.title'),
                Tables\Columns\TextColumn::make('quantity'),
            ])
            ->filters([
                //
            ]);
    }
}
