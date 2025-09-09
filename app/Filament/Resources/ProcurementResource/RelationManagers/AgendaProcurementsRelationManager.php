<?php

declare(strict_types = 1);

namespace App\Filament\Resources\ProcurementResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AgendaProcurementsRelationManager extends RelationManager
{
    protected static string $relationship = 'agendaProcurements';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('agenda_id')
            ->columns([
                Tables\Columns\TextColumn::make('no')
                    ->label('No')
                    ->rowIndex()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('agenda.name')
                    ->label((string) __('Agenda'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label((string) __('Start Date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label((string) __('End Date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\BooleanColumn::make('is_submission_needed')
                    ->label((string) __('Submission Needed'))
                    ->alignCenter(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label((string) __('Status'))
                    ->alignCenter()
                    ->formatStateUsing(fn ($state) => $state->getLabel())
                    ->color(fn ($state) => $state->getColor())
                    ->icon(fn ($state) => $state->getIcon()),

            ])
            ->defaultSort('start_date', 'asc')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('agenda_id')
                    ->label((string) __('Agenda'))
                    ->relationship(
                        name: 'agenda',
                        titleAttribute: 'name',
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabledOn('edit'),
                Forms\Components\DatePicker::make('start_date')
                    ->label((string) __('Start Date'))
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->label((string) __('End Date'))
                    ->required(),
                Forms\Components\Toggle::make('is_submission_needed')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label((string) __('Description'))
                    ->autosize()
                    ->columnSpanFull(),
            ]);
    }
}
