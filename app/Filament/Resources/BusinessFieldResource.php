<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Concerns\Resource\Gate;
use App\Filament\Resources\BusinessFieldResource\Pages;
use App\Models\BusinessField;
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

class BusinessFieldResource extends Resource
{
    use Gate {
        Gate::defineGates insteadof HasHexaLite;
    }
    use HasHexaLite;

    protected static ?string $model = BusinessField::class;

    protected static ?string $modelLabel = 'Business Field';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?int $navigationSort = 3;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBusinessFields::route('/'),
            'create' => Pages\CreateBusinessField::route('/create'),
            'view' => Pages\ViewBusinessField::route('/{record}'),
            'edit' => Pages\EditBusinessField::route('/{record}/edit'),
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
        // // $withoutGlobalScope = ! Auth::user()?->can(static::getModelLabel() . '.withoutGlobalScope');

        return $table
            // ->deferLoading()
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('parent.name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ColorColumn::make('text_color')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ColorColumn::make('background_color')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('is_system')
                    ->boolean()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\Textarea::make('description'),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('code'),
                                Forms\Components\Toggle::make('is_active')
                                    ->inline(false)
                                    ->required()
                                    ->default(true),
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
