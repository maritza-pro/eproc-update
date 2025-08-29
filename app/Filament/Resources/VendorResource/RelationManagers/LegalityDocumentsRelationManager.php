<?php

declare(strict_types=1);

namespace App\Filament\Resources\VendorResource\RelationManagers;

use App\Enums\VendorDocumentType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;


class LegalityDocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'vendorDocuments';

    protected static ?string $title = 'Legality';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')->label('Type')->options(VendorDocumentType::options('legality'))->preload()->required(),
                Forms\Components\Hidden::make('category')->default('legality'),
                Forms\Components\TextInput::make('document_number')->label('Document Number')->nullable(),
                Forms\Components\DatePicker::make('issue_date')->label('Issue Date')->nullable(),
                Forms\Components\DatePicker::make('expiry_date')->label('Expiry Date')->nullable(),
                Forms\Components\SpatieMediaLibraryFileUpload::make('vendor_document_attachment')
                    ->collection('vendor_document_attachment')
                    ->maxFiles(1)
                    ->label('Attachment (PDF, max 2MB)')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(2048)
                    ->downloadable()
                    ->hiddenOn('view'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('category', 'legality'))
            ->recordTitleAttribute('type')
            ->columns([
                Tables\Columns\TextColumn::make('document_number')->label('Document Number'),
                Tables\Columns\TextColumn::make('type')->label('Document Type'),
                Tables\Columns\TextColumn::make('issue_date')->label('Issue Date')->date('d M Y'),
                Tables\Columns\TextColumn::make('expiry_date')->label('Expiry Date')->date('d M Y'),
                Tables\Columns\ViewColumn::make('vendor_document_attachment')
                    ->label('Attachment')
                    ->viewData([
                        'collectionName' => 'vendor_document_attachment',
                    ])
                    ->view('filament.forms.components.table-attachment-viewer'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add')
                    ->icon('heroicon-m-plus'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No Documents')
            ->emptyStateDescription('Create a document to get started.');
    }
}
