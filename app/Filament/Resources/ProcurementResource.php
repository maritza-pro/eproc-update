<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Concerns\Resource\Gate;
use App\Filament\Resources\ProcurementResource\Pages;
use App\Filament\Resources\ProcurementResource\RelationManagers\ItemsRelationManager;
use App\Models\Procurement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Hexters\HexaLite\HasHexaLite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;

class ProcurementResource extends Resource
{
    use Gate {
        Gate::defineGates insteadof HasHexaLite;
    }
    use HasHexaLite;

    protected static ?string $model = Procurement::class;

    protected static ?string $modelLabel = 'Procurement';

    protected static ?string $navigationGroup = 'Procurement';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 1;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProcurements::route('/'),
            'create' => Pages\CreateProcurement::route('/create'),
            'view' => Pages\ViewProcurement::route('/{record}'),
            'edit' => Pages\EditProcurement::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
            ActivitylogRelationManager::class,
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            // ->deferLoading()
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label((string) __('Number'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label((string) __('Title'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('procurementType.name')
                    ->label((string) __('Type'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('procurementType.name')
                    ->label((string) __('Type'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('businessField.name')
                    ->label((string) __('Business Field'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label((string) __('Start Date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label((string) __('End Date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => $state->getLabel())
                    ->color(fn ($state) => $state->getColor())
                    ->icon(fn ($state) => $state->getIcon()),
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
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('number')
                                    ->label((string) __('Procurement Number'))
                                    ->required(),
                                Forms\Components\TextInput::make('title')
                                    ->required(),
                                Forms\Components\Select::make('type_id')
                                    ->relationship(
                                        name: 'procurementType',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Builder $query): Builder => $query->where('is_active', true))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->label((string) __('Type')),
                                Forms\Components\TextInput::make('quantity')
                                    ->label((string) __('Quantity'))
                                    ->numeric()
                                    ->rule('integer')
                                    ->minValue(0),
                                Forms\Components\Select::make('method_id')
                                    ->relationship(
                                        name: 'procurementMethod',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Builder $query): Builder => $query->where('is_active', true))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->label((string) __('Method')),
                                Forms\Components\Select::make('business_field_id')
                                    ->relationship(
                                        name: 'businessField',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Builder $query): Builder => $query->where('is_active', true))
                                    ->searchable()
                                    ->preload()
                                    ->label((string) __('Business Field')),
                                Forms\Components\DatePicker::make('start_date')
                                    ->label((string) __('Start Date'))
                                    ->required()
                                    ->beforeOrEqual('end_date'),
                                Forms\Components\DatePicker::make('end_date')
                                    ->label((string) __('End Date'))
                                    ->afterOrEqual('start_date'),
                                Forms\Components\TextInput::make('value')
                                    ->label((string) __('Project Value'))
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->numeric()
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('description')
                                    ->label((string) __('Description'))
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function canViewAny(): bool
    {
        if (Auth::user()->can(static::getModelLabel() . '.withoutGlobalScope') && Auth::user()->can(static::getModelLabel() . '.viewAny')) {
            return true;
        }

        return Auth::user()->can(static::getModelLabel() . '.viewAny') && Auth::user()?->vendor?->is_blacklisted === false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
