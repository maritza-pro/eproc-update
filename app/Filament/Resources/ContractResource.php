<?php

declare(strict_types=1);

namespace App\Filament\Resources;

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

class ContractResource extends Resource
{
    use HasHexaLite;

    protected static ?string $modelLabel = 'Contract';

    protected static ?string $model = Contract::class;

    protected static ?string $navigationGroup = 'Procurement';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';

    public function defineGates(): array
    {
        return [
            'Contract.index' => __('Allows viewing the Contract list'),
            'Contract.view' => __('Allows viewing Contract detail'),
            'Contract.create' => __('Allows creating a new Contract'),
            'Contract.update' => __('Allows updating Contracts'),
            'Contract.delete' => __('Allows deleting Contracts'),
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
                                Forms\Components\Select::make('procurement_id')
                                    ->relationship('procurement', 'title')
                                    ->required(),
                                Forms\Components\Select::make('vendor_id')
                                    ->relationship('vendor', 'id')
                                    ->required(),
                                Forms\Components\TextInput::make('contract_number')
                                    ->required(),
                                Forms\Components\DatePicker::make('signed_date')
                                    ->required(),
                                Forms\Components\TextInput::make('value')
                                    ->required()
                                    ->numeric(),
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
                Tables\Columns\TextColumn::make('procurement.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('vendor.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contract_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('signed_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
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
            'index' => Pages\ListContracts::route('/'),
            'create' => Pages\CreateContract::route('/create'),
            'view' => Pages\ViewContract::route('/{record}'),
            'edit' => Pages\EditContract::route('/{record}/edit'),
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
