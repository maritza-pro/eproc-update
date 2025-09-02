<?php

declare(strict_types = 1);

namespace App\Filament\Resources\VendorResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BankVendorsRelationManager extends RelationManager
{
    protected static string $relationship = 'bankVendors';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('account_name')
            ->columns([
                Tables\Columns\BooleanColumn::make('is_default'),
                Tables\Columns\TextColumn::make('bank.name'),
                Tables\Columns\BooleanColumn::make('is_active'),
                Tables\Columns\TextColumn::make('account_name'),
                Tables\Columns\TextColumn::make('account_number'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalFooterActionsAlignment(Alignment::End),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalFooterActionsAlignment(Alignment::End),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('bank_id')
                    ->label('Bank Name')
                    ->relationship(
                        name: 'bank',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query): Builder => $query->where('is_active', true))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabledOn('edit'),

                Forms\Components\TextInput::make('account_name')
                    ->required()
                    ->maxLength(255)
                    ->label('Account Name'),

                Forms\Components\TextInput::make('account_number')
                    ->required()
                    ->maxLength(255)
                    ->label('Account Number'),

                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(true),
                Forms\Components\Toggle::make('is_default')
                    ->required()
                    ->default(false),
            ]);
    }
}
