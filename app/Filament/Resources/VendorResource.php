<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Filament\Resources\VendorResource\Pages;
use App\Models\Vendor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Hexters\HexaLite\HasHexaLite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;

class VendorResource extends Resource
{
    use HasHexaLite;

    protected static ?string $model = Vendor::class;

    protected static ?string $modelLabel = 'Vendor';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public function defineGates(): array
    {
        return [
            "{$this->getModelLabel()}.viewAny" => "Allows viewing the {$this->getModelLabel()} list",
            "{$this->getModelLabel()}.view" => "Allows viewing {$this->getModelLabel()} detail",
            "{$this->getModelLabel()}.create" => "Allows creating a new {$this->getModelLabel()}",
            "{$this->getModelLabel()}.edit" => "Allows updating {$this->getModelLabel()}",
            "{$this->getModelLabel()}.delete" => "Allows deleting {$this->getModelLabel()}",
            "{$this->getModelLabel()}.withoutGlobalScope" => "Allows viewing {$this->getModelLabel()} without global scope",
        ];
    }

    public static function canCreate(): bool
    {
        if (Auth::user()->can(static::getModelLabel() . '.withoutGlobalScope')) {
            return true;
        }

        return Auth::user()->can(static::getModelLabel() . '.create') && self::$model::where('user_id', Auth::id())->count() < 1;
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::user()->can(static::getModelLabel() . '.withoutGlobalScope') ||
            (Auth::user()->can(static::getModelLabel() . '.delete') && $record->user_id == Auth::id());
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::user()->can(static::getModelLabel() . '.withoutGlobalScope') ||
            (Auth::user()->can(static::getModelLabel() . '.edit') && $record->user_id == Auth::id());
    }

    public static function canView(Model $record): bool
    {
        return Auth::user()->can(static::getModelLabel() . '.withoutGlobalScope') ||
            (Auth::user()->can(static::getModelLabel() . '.view') && $record->user_id == Auth::id());
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->can(static::getModelLabel() . '.viewAny');
    }

    public static function form(Form $form): Form
    {
        $disableUserSelect = ! Auth::user()->can(static::getModelLabel() . '.withoutGlobalScope');

        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('company_name')
                                    ->required(),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required(),
                                Forms\Components\TextInput::make('phone')
                                    ->tel(),
                                Forms\Components\TextInput::make('tax_number'),
                                Forms\Components\TextInput::make('business_number'),
                                Forms\Components\TextInput::make('license_number'),
                                Forms\Components\Toggle::make('is_verified')
                                    ->required()
                                    ->disabled($disableUserSelect),
                                Forms\Components\Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->required()
                                    ->searchable()
                                    ->default($disableUserSelect ? Auth::id() : null)
                                    ->disabled($disableUserSelect)
                                    ->dehydrated(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVendors::route('/'),
            'create' => Pages\CreateVendor::route('/create'),
            'view' => Pages\ViewVendor::route('/{record}'),
            'edit' => Pages\EditVendor::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->unless(Auth::user()->can(static::getModelLabel() . '.withoutGlobalScope'), function (Builder $query) {
                    $query->where('user_id', Auth::id());
                });
            })
            ->columns([
                Tables\Columns\IconColumn::make('is_verified')
                    ->boolean()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('company_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tax_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('business_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('license_number')
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
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
}
