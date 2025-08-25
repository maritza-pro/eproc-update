<?php

declare(strict_types = 1);

namespace App\Filament\Resources\VendorResource\Components;

use Filament\Forms;
use Filament\Forms\Components\Tabs\Tab;

// TODO : ganti jadi ContactsTab langsung
class ContactsTab
{
    public static function make(): Tab
    {
        return Tab::make('PIC Contacts')
            ->schema([
                Forms\Components\Repeater::make('vendorContacts')
                    ->relationship()
                    ->label('')
                    ->addActionLabel('Add PIC Contact')
                    ->collapsible()
                    ->collapsed()
                    ->defaultItems(0)
                    ->itemLabel(function (array $state): ?string {
                        $parts = [];

                        if (! empty($state['name'])) {
                            $firstName = explode(' ', $state['name'])[0];
                            $parts[] = $firstName;
                        }

                        if (! empty($state['position'])) {
                            $parts[] = $state['position'];
                        }

                        if (! empty($parts)) {
                            return implode(' · ', $parts);
                        }

                        return 'New PIC Contact';
                    })
                    ->schema([
                        Forms\Components\Grid::make(2)
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

                                Forms\Components\View::make('vendor_contact_attachment_viewer')
                                    ->viewData([
                                        'collectionName' => 'vendor_contact_attachment',
                                        'viewLabel' => 'Contact Attachment',
                                    ])
                                    ->view('filament.forms.components.attachment-viewer')
                                    ->visibleOn('view'),

                                Forms\Components\SpatieMediaLibraryFileUpload::make('vendor_contact_attachment')
                                    ->collection('vendor_contact_attachment')
                                    ->maxFiles(1)
                                    ->label('Contact Attachment (JPEG, PNG, PDF, max 2MB)')
                                    ->acceptedFileTypes(['image/*', 'application/pdf'])
                                    ->maxSize(2048)
                                    ->downloadable()
                                    ->hiddenOn('view'),
                            ]),
                    ]),
            ]);
    }
}
