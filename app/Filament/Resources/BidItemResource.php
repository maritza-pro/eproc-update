<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Concerns\Resource\Gate;
use App\Filament\Resources\BidItemResource\Pages;
use App\Models\BidItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Hexters\HexaLite\HasHexaLite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;

class BidItemResource extends Resource
{
    use Gate {
        Gate::defineGates insteadof HasHexaLite;
    }
    use HasHexaLite;

    protected static ?string $model = BidItem::class;

    protected static ?string $modelLabel = 'Bid Item';

    protected static ?string $navigationGroup = 'Bidding';

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?int $navigationSort = 3;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBidItems::route('/'),
            'create' => Pages\CreateBidItem::route('/create'),
            'view' => Pages\ViewBidItem::route('/{record}'),
            'edit' => Pages\EditBidItem::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            ActivitylogRelationManager::class,
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            // ->deferLoading()
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('bid.vendor.company_name')
                    ->label((string) __('Vendor'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bid.procurement.title')
                    ->label((string) __('Procurement'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('procurementItem.product.name')
                    ->label((string) __('Product'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit_price')
                    ->label((string) __('Unit Price'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('offered_quantity')
                    ->label((string) __('Offered Quantity'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label((string) __('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label((string) __('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label((string) __('Deleted At'))
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('bid_id')
                                    ->label((string) __('Bid'))
                                    ->relationship('bid', 'id')
                                    ->required()
                                    ->searchable(),
                                Forms\Components\Select::make('procurement_item_id')
                                    ->label((string) __('Procurement Item'))
                                    ->relationship('procurementItem', 'id')
                                    ->required()
                                    ->searchable(),
                                Forms\Components\TextInput::make('unit_price')
                                    ->label((string) __('Unit Price'))
                                    ->required()
                                    ->numeric(),
                                Forms\Components\TextInput::make('offered_quantity')
                                    ->label((string) __('Offered Quantity'))
                                    ->numeric(),
                                Forms\Components\Textarea::make('notes')
                                    ->label((string) __('Notes'))
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
