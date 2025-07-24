<?php

declare(strict_types = 1);

namespace App\Filament\Resources\SurveyResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    // protected static ?string $title = 'question';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->required()
                    ->options(array_combine(\App\Models\SurveyQuestion::TYPES, \App\Models\SurveyQuestion::TYPES))
                    ->default(\App\Models\SurveyQuestion::TYPE_TEXT),
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
            ]);
    }

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
                //
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
}
