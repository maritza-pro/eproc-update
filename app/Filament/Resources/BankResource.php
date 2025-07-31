<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\BankResource\Pages;
use App\Filament\Resources\BankResource\RelationManagers;
use App\Models\Bank;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Concerns\Resource\Gate;
use Illuminate\Support\Facades\Auth;
use Hexters\HexaLite\HasHexaLite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;
use Filament\Forms\Components\Card;


class BankResource extends Resource
{
    use Gate {
        Gate::defineGates insteadof HasHexaLite;
    }
    use HasHexaLite;

    protected static ?string $modelLabel = 'Bank';

    protected static ?string $model = Bank::class;

	protected static ?string $navigationGroup = 'Master Data';


    protected static ?string $navigationIcon = 'heroicon-o-banknotes';



    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Card::make()
                ->schema([
                    Forms\Components\TextInput::make('bank_name')
                        ->required(),
                    Forms\Components\TextInput::make('bank_account_name')
                        ->label('Nama Pemilik Rekening')
                        ->required(),
                    Forms\Components\TextInput::make('bank_account_number')
                        ->label('Nomor Rekening')
                        ->numeric()
                        ->required(),
                    Forms\Components\TextInput::make('bank_branch')
                        ->label('Cabang Bank'),
                ])
        ]);
}

    public static function table(Table $table): Table
	{
		return $table
			->columns([
				Tables\Columns\TextColumn::make('bank_name')
					->searchable()->sortable(),
				Tables\Columns\TextColumn::make('bank_account_name')
					->label('Nama Pemilik Rekening')
					->searchable(),
				Tables\Columns\TextColumn::make('bank_account_number')
					->label('Nomor Rekening')
					->searchable()
					->badge(),
				Tables\Columns\TextColumn::make('created_at')
					->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
			])
			->filters([
				//
			])
			->actions([
				Tables\Actions\EditAction::make(),
				Tables\Actions\DeleteAction::make(),
				Tables\Actions\ViewAction::make(),
			])
			->bulkActions([
				Tables\Actions\BulkActionGroup::make([
					Tables\Actions\DeleteBulkAction::make(),
				]),
			]);
	}

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBanks::route('/'),
            'create' => Pages\CreateBank::route('/create'),
			'view' => Pages\ViewBank::route('/{record}'),
            'edit' => Pages\EditBank::route('/{record}/edit'),
        ];
    }
}
