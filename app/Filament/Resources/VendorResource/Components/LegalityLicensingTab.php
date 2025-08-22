<?php

declare(strict_types = 1);

namespace App\Filament\Resources\VendorResource\Components;

use Filament\Forms;
use Filament\Forms\Components\Tabs\Tab;

class LegalityLicensingTab
{
    public static function make(): Tab
    {
        return Tab::make('Legality & Licensing')
            ->schema([
                Forms\Components\Group::make()
                    ->relationship(
                        'vendorDeed',
                        condition: fn (?array $state): bool => collect($state ?? [])
                            ->filter(fn ($v) => filled($v))
                            ->isNotEmpty()
                    )
                    ->schema([
                        Forms\Components\Section::make('Deed Information')
                            ->schema([
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('deed_number')->label('Deed Number')->nullable(),
                                    Forms\Components\DatePicker::make('deed_date')->label('Deed Date')->nullable(),

                                    Forms\Components\TextInput::make('deed_notary_name')->label('Notary Name')->nullable(),
                                    Forms\Components\TextInput::make('approval_number')->label('Approval Number (Kemenkumham)')->nullable(),

                                    Forms\Components\TextInput::make('latest_amendment_number')->label('Latest Amendment Number')->nullable(),
                                    Forms\Components\DatePicker::make('latest_amendment_date')->label('Latest Amendment Date')->nullable(),

                                    Forms\Components\TextInput::make('latest_amendment_notary')->label('Latest Amendment Notary')->nullable(),
                                    Forms\Components\TextInput::make('latest_approval_number')->label('Latest Approval Number (Kemenkumham)')->nullable(),

                                    Forms\Components\View::make('vendor_deed_attachment_viewer')
                                        ->viewData([
                                            'collectionName' => 'vendor_deed_attachment',
                                            'viewLabel' => 'Deed Attachment',
                                        ])
                                        ->view('filament.forms.components.attachment-viewer')
                                        ->visibleOn('view'),
                                    Forms\Components\SpatieMediaLibraryFileUpload::make('vendor_deed_attachment')
                                        ->collection('vendor_deed_attachment')
                                        ->label('Deed Attachment (PDF, max 2MB)')
                                        ->acceptedFileTypes(['application/pdf'])
                                        ->maxSize(2048)
                                        ->maxFiles(1)
                                        ->downloadable()
                                        ->hiddenOn('view'),
                                ]),
                            ]),
                    ]),
                Forms\Components\Group::make()
                    ->relationship('vendorBusinessLicense')
                    ->schema([
                        Forms\Components\Section::make('Business License Information')
                            ->schema([
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('license_number')->label('License Number')->nullable(),
                                    Forms\Components\DatePicker::make('issued_at')->label('Issued Date')->nullable(),

                                    Forms\Components\TextInput::make('issued_by')->label('Issued By')->nullable(),
                                    Forms\Components\DatePicker::make('expires_at')->label('Expires Date')->nullable(),

                                    Forms\Components\View::make('vendor_license_attachment_viewer')
                                        ->viewData([
                                            'collectionName' => 'vendor_license_attachment',
                                            'viewLabel' => 'Business License Attachment',
                                        ])
                                        ->view('filament.forms.components.attachment-viewer')
                                        ->visibleOn('view'),
                                    Forms\Components\SpatieMediaLibraryFileUpload::make('vendor_license_attachment')
                                        ->collection('vendor_license_attachment')
                                        ->label('Business License Attachment (PDF, max 2MB)')
                                        ->acceptedFileTypes(['application/pdf'])
                                        ->maxSize(2048)
                                        ->maxFiles(1)
                                        ->downloadable()
                                        ->hiddenOn('view'),
                                ]),
                            ]),
                    ]),
                Forms\Components\Group::make()
                    ->relationship('vendorTaxRegistration')
                    ->schema([
                        Forms\Components\Section::make('Tax Registration Certificate Information')
                            ->schema([
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('name')->label('Name')->nullable(),
                                    Forms\Components\TextInput::make('certificate_number')->label('Certificate Number')->nullable(),

                                    Forms\Components\TextInput::make('confirmation_status')->label('Confirmation Status')->nullable(),
                                    Forms\Components\Select::make('tax_obligation')
                                        ->label('Tax Obligation')
                                        ->options([
                                            'collecting' => 'Collecting',
                                            'depositing' => 'Depositing',
                                            'reporting' => 'Reporting VAT/Luxury Tax',
                                        ]),

                                    Forms\Components\TextInput::make('registered_tax_office')
                                        ->label('Registered Tax Office')
                                        ->nullable(),
                                    Forms\Components\Textarea::make('address')
                                        ->label('Address')
                                        ->autosize()
                                        ->nullable(),

                                    Forms\Components\View::make('vendor_tax_registration_attachment_viewer')
                                        ->viewData([
                                            'collectionName' => 'vendor_tax_registration_attachment',
                                            'viewLabel' => 'Tax Registration Certificate Attachment',
                                        ])
                                        ->view('filament.forms.components.attachment-viewer')
                                        ->visibleOn('view'),
                                    Forms\Components\SpatieMediaLibraryFileUpload::make('vendor_tax_registration_attachment')
                                        ->collection('vendor_tax_registration_attachment')
                                        ->label('Tax Registration Certificate Attachment (PDF, max 2MB)')
                                        ->acceptedFileTypes(['application/pdf'])
                                        ->maxSize(2048)
                                        ->maxFiles(1)
                                        ->downloadable()
                                        ->hiddenOn('view'),
                                ]),
                            ]),
                    ]),
                Forms\Components\Group::make()
                    ->relationship('vendorBusinessCertificate')
                    ->schema([
                        Forms\Components\Section::make('Business Certificate Information')
                            ->schema([
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('certificate_number')->label('Certificate Number')->nullable(),
                                    Forms\Components\DatePicker::make('issued_at')->label('Issued Date')->nullable(),

                                    Forms\Components\TextInput::make('issued_by')->label('Issued By')->nullable(),
                                    Forms\Components\DatePicker::make('expires_at')->label('Expires Date')->nullable(),

                                    Forms\Components\TextInput::make('classification')->label('Classification')->nullable(),
                                    Forms\Components\View::make('vendor_certificate_attachment_viewer')
                                        ->viewData([
                                            'collectionName' => 'vendor_certificate_attachment',
                                            'viewLabel' => 'Business Certificate Attachment',
                                        ])
                                        ->view('filament.forms.components.attachment-viewer')
                                        ->visibleOn('view'),
                                    Forms\Components\SpatieMediaLibraryFileUpload::make('vendor_certificate_attachment')
                                        ->collection('vendor_certificate_attachment')
                                        ->label('Business Certificate Attachment (PDF, max 2MB)')
                                        ->acceptedFileTypes(['application/pdf'])
                                        ->maxSize(2048)
                                        ->maxFiles(1)
                                        ->downloadable()
                                        ->hiddenOn('view'),
                                ]),
                            ]),
                    ]),
            ]);
    }
}