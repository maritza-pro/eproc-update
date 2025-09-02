<?php

declare(strict_types = 1);

namespace App\Filament\Resources\VendorResource\RelationManagers;

use App\Enums\VendorDocumentType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LegalityDocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'vendorDocuments';

    protected static ?string $title = 'Legality';

    protected function dynamicSchema(?string $documentType): array
    {
        $type = $documentType ? VendorDocumentType::from($documentType) : null;

        return match ($type) {
            VendorDocumentType::DeedInformation => [
                Forms\Components\TextInput::make('document_number')->label('Deed Number')->nullable(),
                Forms\Components\DatePicker::make('issue_date')->label('Deed Date')->nullable(),
                Forms\Components\TextInput::make('properties.notary_name')->label('Notary Name')->nullable(),
                Forms\Components\TextInput::make('properties.latest_amendment_number')->label('Latest Amendment Number')->nullable(),
                Forms\Components\DatePicker::make('properties.latest_amendment_date')->label('Latest Amendment Date')->nullable(),
                Forms\Components\TextInput::make('properties.latest_amendment_notary')->label('Latest Amendment Notary')->nullable(),
            ],
            VendorDocumentType::PengesahanKemenkumham => [
                Forms\Components\TextInput::make('document_number')->label('Approval Number')->nullable(),
                Forms\Components\DatePicker::make('issue_date')->label('Issue Date')->nullable(),
            ],

            default => [],
        };
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
                    ->modalHeading('Add Legality Document')
                    ->icon('heroicon-m-plus')
                    ->createAnother(false)
                    ->modalFooterActionsAlignment(Alignment::End),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalFooterActionsAlignment(Alignment::End),
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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema(fn (Get $get): array => [
                        Forms\Components\Select::make('type')
                            ->label('Type')
                            ->options(VendorDocumentType::options('legality'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->disabledOn('edit')
                            ->afterStateUpdated(fn (callable $set) => $set('properties', [])),
                        Forms\Components\Hidden::make('category')->default('legality'),
                        Forms\Components\Hidden::make('properties')->default([]),

                        ...$this->dynamicSchema($get('type')),

                        Forms\Components\SpatieMediaLibraryFileUpload::make('vendor_document_attachment')
                            ->collection('vendor_document_attachment')
                            ->maxFiles(1)
                            ->label('Attachment (PDF, max 2MB)')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(2048)
                            ->downloadable()
                            ->visible(fn (Get $get) => filled($get('type'))),
                    ]),
            ]);
    }
}
