<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Concerns\Resource\Gate;
use App\Filament\Resources\ContractResource\Pages;
use App\Models\Contract;
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

class ContractResource extends Resource
{
    use Gate {
        Gate::defineGates insteadof HasHexaLite;
    }
    use HasHexaLite;

    protected static ?string $model = Contract::class;

    protected static ?string $modelLabel = 'Contract';

    protected static ?string $navigationGroup = 'Procurement';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';

    protected static ?int $navigationSort = 2;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContracts::route('/'),
            'create' => Pages\CreateContract::route('/create'),
            'view' => Pages\ViewContract::route('/{record}'),
            'edit' => Pages\EditContract::route('/{record}/edit'),
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
                Tables\Columns\TextColumn::make('vendor.id')
                    ->label((string) __('Vendor'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contract_number')
                    ->label((string) __('Contract Number'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('signed_date')
                    ->label((string) __('Signed Date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->label((string) __('Value'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label((string) __('Status'))
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
                                Forms\Components\Select::make('procurement_id')
                                    ->label((string) __('Procurement'))
                                    ->relationship('procurement', 'title')
                                    ->required()
                                    ->searchable(),
                                Forms\Components\Select::make('vendor_id')
                                    ->label((string) __('Vendor'))
                                    ->relationship('vendor', 'id')
                                    ->required()
                                    ->searchable(),
                                Forms\Components\TextInput::make('contract_number')
                                    ->label((string) __('Contract Number'))
                                    ->required(),
                                Forms\Components\DatePicker::make('signed_date')
                                    ->label((string) __('Signed Date'))
                                    ->required(),
                                Forms\Components\TextInput::make('value')
                                    ->label((string) __('Value'))
                                    ->required()
                                    ->numeric(),
                                Forms\Components\TextInput::make('status')
                                    ->label((string) __('Status'))
                                    ->required(),
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
