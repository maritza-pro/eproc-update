<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Filament\Resources\BidResource\Pages;
use App\Filament\Resources\BidResource\RelationManagers\ItemsRelationManager;
use App\Models\Bid;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Hexters\HexaLite\HasHexaLite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;

class BidResource extends Resource
{
    use HasHexaLite;

    protected static ?string $model = Bid::class;

    protected static ?string $modelLabel = 'Bid';

    protected static ?string $navigationGroup = 'Bidding';

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected static ?int $navigationSort = 1;

    public function defineGates(): array
    {
        return [
            "{$this->getModelLabel()}.viewAny" => "Allows viewing the {$this->getModelLabel()} list",
            "{$this->getModelLabel()}.view" => "Allows viewing {$this->getModelLabel()} detail",
            "{$this->getModelLabel()}.create" => "Allows creating a new {$this->getModelLabel()}",
            "{$this->getModelLabel()}.edit" => "Allows updating {$this->getModelLabel()}",
            "{$this->getModelLabel()}.delete" => "Allows deleting {$this->getModelLabel()}",
            "{$this->getModelLabel()}.withoutGlobalScope" => "Allows viewing {$this->getModelLabel()} without global scope",
        ];
    }

    public static function canCreate(): bool
    {
        return Auth::user()->can(static::getModelLabel() . '.create');
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::user()->can(static::getModelLabel() . '.delete');
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::user()->can(static::getModelLabel() . '.edit');
    }

    public static function canView(Model $record): bool
    {
        return Auth::user()->can(static::getModelLabel() . '.view');
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->can(static::getModelLabel() . '.viewAny');
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
                                    ->options(\App\Models\Vendor::pluck('company_name', 'id'))
                                    ->required()
                                    ->searchable(),
                                Forms\Components\Select::make('procurement_id')
                                    ->relationship('procurement', 'title')
                                    ->options(\App\Models\Procurement::pluck('title', 'id'))
                                    ->required()
                                    ->searchable(),
                                Forms\Components\Textarea::make('notes')
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('status')
                                    ->required()
                                    ->options(array_combine(Bid::STATUSES, Bid::STATUSES))
                                    ->default('draft'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBids::route('/'),
            'create' => Pages\CreateBid::route('/create'),
            'view' => Pages\ViewBid::route('/{record}'),
            'edit' => Pages\EditBid::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vendor.company_name')
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
}
