<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Concerns\Resource\Gate;
use App\Filament\Resources\SurveyResource\Pages;
use App\Filament\Resources\SurveyResource\RelationManagers\QuestionsRelationManager;
use App\Models\BusinessField;
use App\Models\Survey;
use App\Models\VendorType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Hexters\HexaLite\HasHexaLite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;

class SurveyResource extends Resource
{
    use Gate {
        Gate::defineGates insteadof HasHexaLite;
    }
    use HasHexaLite;

    protected static ?string $model = Survey::class;

    protected static ?string $modelLabel = 'Survey';

    protected static ?string $navigationGroup = 'Surveys';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSurveys::route('/'),
            'create' => Pages\CreateSurvey::route('/create'),
            'view' => Pages\ViewSurvey::route('/{record}'),
            'edit' => Pages\EditSurvey::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            QuestionsRelationManager::class,
            ActivitylogRelationManager::class,
        ];
    }

    public static function table(Table $table): Table
    {
        // // $withoutGlobalScope = ! Auth::user()?->can(static::getModelLabel() . '.withoutGlobalScope');

        return $table
            // ->deferLoading()
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label((string) __('Title'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label((string) __('Category'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('vendorType.name')
                    ->label((string) __('Vendor Type'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('vendorBusiness.name')
                    ->label((string) __('Vendor Business'))
                    ->sortable()
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
                                Forms\Components\Select::make('category_id')
                                    ->label((string) __('Category'))
                                    ->relationship('category', 'name')
                                    ->searchable(),
                                Forms\Components\Select::make('type')
                                    ->label((string) __('Type'))
                                    ->options([
                                        'vendor' => 'Vendor',
                                        'procurement' => 'Procurement',
                                    ])
                                    ->required()
                                    ->reactive(),
                                Forms\Components\Select::make('properties.vendor_type')
                                    ->label((string) __('Vendor Type'))
                                    ->options(fn () => VendorType::query()->pluck('name', 'id'))
                                    ->required(fn ($get): bool => $get('type') === 'vendor')
                                    ->visible(fn ($get): bool => $get('type') === 'vendor')
                                    ->searchable(),
                                Forms\Components\Select::make('properties.vendor_business')
                                    ->label((string) __('Vendor Business'))
                                    ->options(fn () => BusinessField::query()->pluck('name', 'id'))
                                    ->required(fn ($get): bool => $get('type') === 'vendor')
                                    ->visible(fn ($get): bool => $get('type') === 'vendor')
                                    ->searchable(),
                                Forms\Components\TextInput::make('title')
                                    ->label((string) __('Title'))
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('description')
                                    ->label((string) __('Description'))
                                    ->columnSpanFull(),
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
