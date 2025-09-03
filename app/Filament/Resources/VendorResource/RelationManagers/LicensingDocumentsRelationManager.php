<?php

declare(strict_types = 1);

namespace App\Filament\Resources\VendorResource\RelationManagers;

use App\Enums\VendorDocumentType;
use App\Models\VendorBusiness;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LicensingDocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'vendorDocuments';

    protected static ?string $title = 'Licensing';

    protected function dynamicSchema(?string $documentType): array
    {
        $type = $documentType ? VendorDocumentType::from($documentType) : null;

        return match ($type) {
            VendorDocumentType::TradingBusinessLicenseSIUP => [
                Forms\Components\TextInput::make('document_number')->label('SIUP Number')->nullable(),
                Forms\Components\DatePicker::make('issue_date')->label('Issue Date')->nullable(),
                Forms\Components\DatePicker::make('expiry_date')->label('Expiry Date')->nullable(),
                Forms\Components\TextInput::make('properties.issuing_authority')->label('Issuing Authority')->nullable(),
                Forms\Components\Select::make('properties.business_field_id')
                    ->label('Business Field')
                    ->options(fn () => VendorBusiness::query()
                        ->where('is_active', true)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray()
                    ),
            ],
            VendorDocumentType::CompanyRegistrationTDP => [
                Forms\Components\TextInput::make('document_number')->label('TDP Number')->nullable(),
                Forms\Components\DatePicker::make('issue_date')->label('Issue Date')->nullable(),
                Forms\Components\DatePicker::make('expiry_date')->label('Expiry Date')->nullable(),
                Forms\Components\TextInput::make('properties.issuing_authority')->label('Issuing Authority')->nullable(),
                Forms\Components\TextInput::make('properties.company_name')->label('Company Name')->nullable(),
            ],
            VendorDocumentType::BusinessDomicileLetterSKDU => [
                Forms\Components\TextInput::make('document_number')->label('SKDU Number')->nullable(),
                Forms\Components\DatePicker::make('issue_date')->label('Issue Date')->nullable(),
                Forms\Components\DatePicker::make('expiry_date')->label('Expiry Date')->nullable(),
                Forms\Components\TextInput::make('properties.issuing_authority')->label('Issuing Authority')->nullable(),
                Forms\Components\TextInput::make('properties.business_address')->label('Business Address')->nullable(),
            ],
            VendorDocumentType::TaxableEntrepreneurSPPKP => [
                Forms\Components\TextInput::make('document_number')->label('SPPKP Number')->nullable(),
                Forms\Components\DatePicker::make('issue_date')->label('Confirmation Date')->nullable(),
                Forms\Components\TextInput::make('properties.company_name')->label('Company Name')->nullable(),
                Forms\Components\TextInput::make('properties.address')->label('Address')->nullable(),
            ],
            VendorDocumentType::BusinessIdentificationNumberNIB => [
                Forms\Components\TextInput::make('document_number')->label('NIB Number')->nullable(),
                Forms\Components\DatePicker::make('issue_date')->label('Issue Date')->nullable(),
                Forms\Components\Select::make('properties.risk_level')->label('Risk Level')->nullable()
                    ->options([
                        'low' => 'Low',
                        'low-medium' => 'Low to Medium',
                        'medium-high' => 'Medium to High',
                        'high' => 'High',
                    ]),
                 Forms\Components\Select::make('properties.business_field_id')
                    ->label('Business Field')
                    ->options(fn () => VendorBusiness::query()
                        ->where('is_active', true)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray()
                    ),
            ],
            VendorDocumentType::HinderOrdonantieHO => [
                Forms\Components\TextInput::make('document_number')->label('HO Number')->nullable(),
                Forms\Components\DatePicker::make('issue_date')->label('Issue Date')->nullable(),
                Forms\Components\DatePicker::make('expiry_date')->label('Expiry Date')->nullable(),
                Forms\Components\TextInput::make('properties.issuing_authority')->label('Issuing Authority')->nullable(),
                Forms\Components\TextInput::make('properties.business_location')->label('Business Location')->nullable(),
            ],
            VendorDocumentType::BusinessEntityCertificateSBU => [
                Forms\Components\TextInput::make('document_number')->label('Certificate Number')->nullable(),
                Forms\Components\DatePicker::make('issue_date')->label('Issue Date')->nullable(),
                Forms\Components\DatePicker::make('expiry_date')->label('Expiry Date')->nullable(),
                Forms\Components\TextInput::make('properties.issuing_authority')->label('Issuing Authority')->nullable(),
                Forms\Components\Select::make('properties.qualification')->label('Qualification')->nullable()
                    ->options([
                        'Small' => 'Small',
                        'Medium' => 'Medium',
                        'Large' => 'Large',
                    ]),
                Forms\Components\Select::make('properties.business_field_id')
                    ->label('Business Field')
                    ->options(fn () => VendorBusiness::query()
                        ->where('is_active', true)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray()
                    ),
            ],

            default => [],
        };
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('category', 'licensing'))
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
                    ->modalHeading('Add Licensing Document')
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
                            ->options(VendorDocumentType::options('licensing'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->disabledOn('edit')
                            ->afterStateUpdated(fn (callable $set) => $set('properties', [])),
                        Forms\Components\Hidden::make('category')->default('licensing'),
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
