<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Concerns\Resource\Gate;
use App\Filament\Resources\ProcurementItemResource\Pages;
use App\Models\ProcurementItem;
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

class ProcurementItemResource extends Resource
{
    use Gate {
        Gate::defineGates insteadof HasHexaLite;
    }
    use HasHexaLite;

    protected static ?string $model = ProcurementItem::class;

    protected static ?string $modelLabel = 'Procurement Item';

    protected static ?string $navigationGroup = 'Procurement';

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?int $navigationSort = 4;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProcurementItems::route('/'),
            'create' => Pages\CreateProcurementItem::route('/create'),
            'view' => Pages\ViewProcurementItem::route('/{record}'),
            'edit' => Pages\EditProcurementItem::route('/{record}/edit'),
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
                Tables\Columns\TextColumn::make('procurement.title')
                    ->label((string) __('Procurement'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label((string) __('Product'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label((string) __('Quantity'))
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
                                Forms\Components\Select::make('procurement_id')
                                    ->label((string) __('Procurement'))
                                    ->relationship('procurement', 'title')
                                    ->required()
                                    ->searchable(),
                                Forms\Components\Select::make('product_id')
                                    ->label((string) __('Product'))
                                    ->relationship('product', 'name')
                                    ->required()
                                    ->searchable(),
                                Forms\Components\TextInput::make('quantity')
                                    ->label((string) __('Quantity'))
                                    ->required()
                                    ->numeric(),
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
