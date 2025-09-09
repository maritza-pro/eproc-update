<?php

declare(strict_types = 1);

namespace App\Filament\Clusters\MasterProcurements\Resources;

use App\Concerns\Resource\Gate;
use App\Filament\Clusters\MasterProcurements;
use App\Filament\Clusters\MasterProcurements\Resources\ProcurementAgendaResource\Pages;
use App\Models\ProcurementAgenda;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Hexters\HexaLite\HasHexaLite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;

class ProcurementAgendaResource extends Resource
{
    use Gate {
        Gate::defineGates insteadof HasHexaLite;
    }
    use HasHexaLite;

    protected static ?string $cluster = MasterProcurements::class;

    protected static ?string $model = ProcurementAgenda::class;

    protected static ?string $modelLabel = 'Agenda';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProcurementAgendas::route('/'),
        ];
    }

    public static function table(Table $table): Table
    {
        // $withoutGlobalScope = ! Auth::user()?->can(static::getModelLabel() . '.withoutGlobalScope');

        return $table
            // ->deferLoading()
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label((string) __('Type'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('code')
                    ->label((string) __('Code'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label((string) __('Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label((string) __('Description'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
                    ->label((string) __('Active'))
                    ->boolean()
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('is_system')
                    ->label((string) __('System'))
                    ->boolean()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
                            ->label((string) __('Name'))
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->label((string) __('Description')),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('code')
                                    ->label((string) __('Code')),
                                Forms\Components\Toggle::make('is_active')
                                    ->label((string) __('Active'))
                                    ->inline(false)
                                    ->required()
                                    ->default(true),
                            ]),
                    ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
