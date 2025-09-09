<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Concerns\Resource\Gate;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers\BidItemsRelationManager;
use App\Filament\Resources\ProductResource\RelationManagers\ProcurementItemsRelationManager;
use App\Models\Currency;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Hexters\HexaLite\HasHexaLite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;

class ProductResource extends Resource
{
    use Gate {
        Gate::defineGates insteadof HasHexaLite;
    }
    use HasHexaLite;

    protected static ?string $model = Product::class;

    protected static ?string $modelLabel = 'Product';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            ProcurementItemsRelationManager::class,
            BidItemsRelationManager::class,
            ActivitylogRelationManager::class,
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            // ->deferLoading()
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label((string) __('Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('self_estimated_price')
                    ->label((string) __('Price'))
                    ->money(fn ($record) => $record->currency?->code)
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency.name')
                    ->label((string) __('Currency'))
                    ->searchable()
                    ->badge(),
                Tables\Columns\TextColumn::make('type')
                    ->label((string) __('Type'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit')
                    ->label((string) __('Unit'))
                    ->searchable(),
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
                                Forms\Components\TextInput::make('name')
                                    ->label((string) __('Name'))
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('type')
                                    ->label((string) __('Type'))
                                    ->required()
                                    ->options(array_combine(Product::TYPES, Product::TYPES))
                                    ->searchable(),
                                Forms\Components\TextInput::make('unit')
                                    ->label((string) __('Unit')),
                                Forms\Components\Select::make('currency_id')
                                    ->label((string) __('Currency'))
                                    ->relationship('currency', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live(),
                                Forms\Components\TextInput::make('self_estimated_price')
                                    ->label((string) __('Estimated Price'))
                                    ->numeric()
                                    ->required()
                                    ->default(0)
                                    ->prefix(fn (Get $get): string => Currency::query()->find($get('currency_id'))?->symbol . ' '),
                                Forms\Components\Textarea::make('description')
                                    ->label((string) __('Description'))
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
