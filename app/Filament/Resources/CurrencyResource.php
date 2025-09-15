<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Concerns\Resource\Gate;
use App\Filament\Resources\CurrencyResource\Pages;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Hexters\HexaLite\HasHexaLite;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;

class CurrencyResource extends Resource
{
    use Gate {
        Gate::defineGates insteadof HasHexaLite;
    }
    use HasHexaLite;

    protected static ?string $model = Currency::class;

    protected static ?string $modelLabel = 'Currency';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCurrencies::route('/'),
            'create' => Pages\CreateCurrency::route('/create'),
            'view' => Pages\ViewCurrency::route('/{record}'),
            'edit' => Pages\EditCurrency::route('/{record}/edit'),
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
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label((string) __('Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->label((string) __('Code'))
                    ->searchable()
                    ->badge(),
                Tables\Columns\TextColumn::make('symbol')
                    ->label((string) __('Symbol'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('decimals')
                    ->label((string) __('Decimals'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('symbol_position')
                    ->label((string) __('Symbol Position'))
                    ->badge()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_default')
                    ->label((string) __('Default'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label((string) __('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label((string) __('Name'))
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('code')
                                    ->label((string) __('ISO Code'))
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(3),
                                Forms\Components\TextInput::make('symbol')
                                    ->label((string) __('Symbol'))
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(5),
                                Forms\Components\TextInput::make('decimals')
                                    ->label((string) __('Decimals'))
                                    ->required()
                                    ->numeric()
                                    ->default(2)
                                    ->minValue(0)
                                    ->maxValue(6),
                                Forms\Components\Select::make('symbol_position')
                                    ->label((string) __('Symbol Position'))
                                    ->options([
                                        'left' => 'Left',
                                        'right' => 'Right',
                                    ])
                                    ->required()
                                    ->default('left'),
                                Forms\Components\Toggle::make('is_default')
                                    ->label((string) __('Default'))
                                    ->required(),
                            ]),
                    ]),
            ]);
    }
}
