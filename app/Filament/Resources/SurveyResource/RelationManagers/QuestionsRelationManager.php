<?php

declare(strict_types = 1);

namespace App\Filament\Resources\SurveyResource\RelationManagers;

use Awcodes\TableRepeater;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    /**
     * Configure the table for the relation manager.
     *
     * Defines the table columns, filters, and actions.
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('question')
            ->columns([
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('question'),
                Tables\Columns\BooleanColumn::make('required')
                    ->alignCenter(),
            ])
            ->filters([

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
                ]),
            ]);
    }

    // protected static ?string $title = 'question';

    /**
     * Build the form for creating or editing a record.
     *
     * Defines the form schema for managing survey questions.
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->required()
                    ->options(array_combine(\App\Models\SurveyQuestion::TYPES, \App\Models\SurveyQuestion::TYPES))
                    ->default(\App\Models\SurveyQuestion::TYPE_TEXT)
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('refresh', now())),
                Forms\Components\TextInput::make('question')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('points')
                    ->numeric()
                    ->default(100),
                Forms\Components\Toggle::make('required')
                    ->inline(false),
                TableRepeater\Components\TableRepeater::make('options')
                    ->relationship('options')
                    ->headers([
                        TableRepeater\Header::make('option'),
                        TableRepeater\Header::make('points'),
                        TableRepeater\Header::make('is_correct'),
                    ])
                    ->schema([
                        Forms\Components\TextInput::make('option')->required(),
                        Forms\Components\TextInput::make('points')->required(),
                        Forms\Components\Toggle::make('is_correct')->inline(false),
                    ])
                    ->columnSpan('full')
                    ->hidden(fn (callable $get): bool => ! in_array($get('type'), ['select', 'radio', 'checkbox'])),
            ]);
    }
}
