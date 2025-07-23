<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Concerns\Resource\Gate;
use App\Filament\Resources\BidResource\Pages;
use App\Filament\Resources\BidResource\RelationManagers\ItemsRelationManager;
use App\Models\Bid;
use Auth;
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
    use Gate {
        Gate::defineGates insteadof HasHexaLite;
    }
    use HasHexaLite;

    protected static ?string $model = Bid::class;

    protected static ?string $modelLabel = 'Bid';

    protected static ?string $navigationGroup = 'Bidding';

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        $withoutGlobalScope = Auth::user()->can(static::getModelLabel() . '.withoutGlobalScope');
        $vendorOptions = \App\Models\Vendor::when(! $withoutGlobalScope, fn (Builder $query) => $query->where('user_id', Auth::id()))
            ->pluck('company_name', 'id');

        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('vendor_id')
                                    ->relationship('vendor', 'id')
                                    ->options($vendorOptions)
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
                                    ->default('draft')
                                    ->disabled(! $withoutGlobalScope)
                                    ->dehydrated(),
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
