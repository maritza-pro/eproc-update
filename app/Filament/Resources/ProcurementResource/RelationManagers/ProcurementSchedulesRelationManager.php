<?php

declare(strict_types = 1);

namespace App\Filament\Resources\ProcurementResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ProcurementSchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'procurementSchedules';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('schedule_id')
            ->reorderable('sequence')
            ->defaultSort('sequence', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('no')
                    ->label((string) __('No'))
                    ->rowIndex()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('schedule.name')
                    ->label((string) __('Schedule'))
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
        $procurement = $this->getOwnerRecord();

        return $form
            ->schema([
                Forms\Components\Select::make('schedule_id')
                    ->label((string) __('Schedule'))
                    ->relationship(
                        name: 'schedule',
                        titleAttribute: 'name',
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabledOn('edit'),
                Forms\Components\DatePicker::make('start_date')
                    ->label((string) __('Start Date'))
                    ->required()
                    ->minDate($procurement->start_date)
                    ->maxDate($procurement->end_date),
                Forms\Components\DatePicker::make('end_date')
                    ->label((string) __('End Date'))
                    ->required()
                    ->afterOrEqual('start_date')
                    ->minDate($procurement->start_date)
                    ->maxDate($procurement->end_date),
                Forms\Components\Toggle::make('is_submission_needed')
                    ->label((string) __('Submission Needed'))
                    ->required(),
                Forms\Components\RichEditor::make('description')
                    ->label((string) __('Description'))
                    ->columnSpanFull(),
            ]);
    }
}
