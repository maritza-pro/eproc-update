<?php

declare(strict_types=1);

namespace App\Filament\Resources\VendorResource\Components;

use Filament\Forms;
use Filament\Forms\Components\Tabs\Tab;

class ExperiencesTab
{
    public static function make(): Tab
    {
        return Forms\Components\Tabs\Tab::make((string) __('Experiences'))
            ->schema([
                Forms\Components\Repeater::make('vendorExperiences')
                    ->relationship()
                    ->label('')
                    ->addActionLabel('Add Experience')
                    ->collapsible()
                    ->collapsed()
                    ->defaultItems(0)
                    ->itemLabel(function (array $state): string {
                        if (! empty($state['project_name'])) {
                            return '- ' . $state['project_name'];
                        }

                        return 'New Experience';
                    })
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('project_name')
                                    ->label((string) __('Project Name'))
                                    ->nullable(),
                                Forms\Components\Select::make('business_field_id')->relationship('businessField', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->nullable()
                                    ->label((string) __('Business Field')),

                                Forms\Components\TextInput::make('location')
                                    ->label((string) __('Project Location'))
                                    ->nullable(),
                                Forms\Components\TextInput::make('stakeholder')
                                    ->label((string) __('Stakeholder'))
                                    ->nullable(),

                                Forms\Components\TextInput::make('contract_number')
                                    ->label((string) __('Contract Number'))
                                    ->nullable(),
                                Forms\Components\TextInput::make('project_value')
                                    ->label((string) __('Project Value'))
                                    ->nullable(),

                                Forms\Components\DatePicker::make('start_date')
                                    ->label((string) __('Start Date'))
                                    ->nullable(),
                                Forms\Components\DatePicker::make('end_date')
                                    ->label((string) __('End Date'))
                                    ->nullable()
                                    ->afterOrEqual('start_date'),

                                Forms\Components\Textarea::make('description')
                                    ->label((string) __('Description'))
                                    ->autosize()
                                    ->nullable()
                                    ->maxLength(100),

                                Forms\Components\View::make('vendor_experience_attachment_viewer')
                                    ->viewData([
                                        'collectionName' => 'vendor_experience_attachment',
                                        'viewLabel' => 'Experience Attachment',
                                    ])
                                    ->view('filament.forms.components.attachment-viewer')
                                    ->visibleOn('view'),
                                Forms\Components\SpatieMediaLibraryFileUpload::make('vendor_experience_attachment')
                                    ->collection('vendor_experience_attachment')
                                    ->maxFiles(1)
                                    ->label((string) __('Experience Attachment (PDF, max 2MB)'))
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->maxSize(2048)
                                    ->downloadable()
                                    ->hiddenOn('view'),
                            ]),
                    ]),
            ]);
    }
}
