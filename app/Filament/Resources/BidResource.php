<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\BidResource\Pages;
use App\Models\Bid;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Hexters\HexaLite\HasHexaLite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;

class BidResource extends Resource
{
    use HasHexaLite;

    protected static ?string $modelLabel = 'Bid';

    protected static ?string $model = Bid::class;

    protected static ?string $navigationGroup = 'Bidding';

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    public function defineGates(): array
    {
        return [
            'Bid.index' => __('Allows viewing the Bid list'),
            'Bid.view' => __('Allows viewing Bid detail'),
            'Bid.create' => __('Allows creating a new Bid'),
            'Bid.update' => __('Allows updating Bids'),
            'Bid.delete' => __('Allows deleting Bids'),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('vendor_id')
                                    ->relationship('vendor', 'id')
                                    ->required(),
                                Forms\Components\Select::make('procurement_id')
                                    ->relationship('procurement', 'title')
                                    ->required(),
                                Forms\Components\Textarea::make('notes')
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('status')
                                    ->required(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vendor.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('procurement.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                ActivityLogTimelineTableAction::make('Activities'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBids::route('/'),
            'create' => Pages\CreateBid::route('/create'),
            'view' => Pages\ViewBid::route('/{record}'),
            'edit' => Pages\EditBid::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
