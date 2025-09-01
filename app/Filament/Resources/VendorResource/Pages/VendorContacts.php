<?php

declare(strict_types = 1);

namespace App\Filament\Resources\VendorResource\Pages;

use App\Filament\Resources\VendorResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VendorContacts extends ManageRelatedRecords
{
    protected static string $resource = VendorResource::class;

    protected static string $relationship = 'vendorContacts';

    protected static ?string $title = 'PIC Contacts';

    public static function getNavigationLabel(): string
    {
        return 'PIC Contacts';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Full Name')
                    ->nullable(),

                Forms\Components\TextInput::make('position')
                    ->label('Job Title / Position')
                    ->nullable(),

                Forms\Components\TextInput::make('phone_number')
                    ->label('Phone Number')
                    ->tel()
                    ->nullable(),

                Forms\Components\TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->nullable(),

                Forms\Components\TextInput::make('identity_number')
                    ->label('National ID (KTP) Number')
                    ->nullable(),

                Forms\Components\SpatieMediaLibraryFileUpload::make('vendor_contact_attachment')
                    ->collection('vendor_contact_attachment')
                    ->maxFiles(1)
                    ->label('Attachment (JPEG, PNG, PDF, max 2MB)')
                    ->acceptedFileTypes(['image/*', 'application/pdf'])
                    ->maxSize(2048)
                    ->downloadable()
                    ->hiddenOn('view'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('position')
                    ->label('Job Title / Position')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('phone_number'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add PIC Contact')
                    ->modalHeading('Add PIC Contact')
                    ->createAnother(false)
                    ->modalFooterActionsAlignment(Alignment::End),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->modalFooterActionsAlignment(Alignment::End),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }
}
