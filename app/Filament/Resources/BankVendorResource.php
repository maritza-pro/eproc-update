<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Concerns\Resource\Gate;
use App\Filament\Resources\BankVendorResource\Pages;
use App\Models\BankVendor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Hexters\HexaLite\HasHexaLite;
use Illuminate\Database\Eloquent\Builder;

class BankVendorResource extends Resource
{
    use Gate {
        Gate::defineGates insteadof HasHexaLite;
    }
    use HasHexaLite;

    protected static ?string $model = BankVendor::class;

    protected static ?string $modelLabel = 'Bank Vendor';

    protected static ?string $navigationGroup = 'Vendors';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 2;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBankVendors::route('/'),
            'create' => Pages\CreateBankVendor::route('/create'),
            'view' => Pages\ViewBankVendor::route('/{record}'),
            'edit' => Pages\EditBankVendor::route('/{record}/edit'),
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
            ->columns([
                Tables\Columns\TextColumn::make('bank.name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Bank'),

                Tables\Columns\TextColumn::make('account_name')
                    ->searchable()
                    ->label('Nama Pemilik'),

                Tables\Columns\TextColumn::make('account_number')
                    ->searchable()
                    ->label('No. Rekening'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('bank_id')
                    ->relationship('bank', 'name')
                    ->label('Filter by Bank'),
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\Select::make('bank_id')
                        ->relationship(
                            name: 'bank',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn (Builder $query): Builder => $query->where('is_active', true))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->label('Nama Bank'),

                    Forms\Components\TextInput::make('account_name')
                        ->required()
                        ->maxLength(255)
                        ->label('Nama Pemilik Rekening'),

                    Forms\Components\TextInput::make('account_number')
                        ->required()
                        ->maxLength(255)
                        ->label('Nomor Rekening'),

                    Forms\Components\TextInput::make('branch_name')
                        ->maxLength(255)
                        ->label('Nama Cabang (Opsional)'),

                    Forms\Components\Toggle::make('is_active')
                        ->required()
                        ->default(true),
                ])->columns(2),
            ]);
    }
}
