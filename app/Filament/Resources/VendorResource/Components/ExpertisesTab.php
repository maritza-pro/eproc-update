<?php

declare(strict_types = 1);

namespace App\Filament\Resources\VendorResource\Components;

use Filament\Forms;
use Filament\Forms\Components\Tabs\Tab;

class ExpertisesTab
{
    public static function make(): Tab
    {
        return Tab::make((string) __('Expertises'))
            ->schema([
                Forms\Components\Repeater::make('vendorExpertises')
                    ->relationship()
                    ->label('')
                    ->addActionLabel('Add Expertise')
                    ->collapsible()
                    ->collapsed()
                    ->columns(1)
                    ->defaultItems(0)
                    ->itemLabel(function (array $state): string {
                        if (! empty($state['expertise'])) {
                            return '- ' . $state['expertise'];
                        }

                        return 'New Expertise';
                    })
                    ->schema([
                        Forms\Components\TextInput::make('expertise')
                            ->label((string) __('Expertise'))
                            ->nullable(),

                        Forms\Components\Select::make('expertise_level')
                            ->label((string) __('Expertise Level'))
                            ->nullable()
                            ->options([
                                'basic' => 'Basic',
                                'intermediate' => 'Intermediate',
                                'expert' => 'Expert',
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->label((string) __('Description'))
                            ->autosize()
                            ->nullable()
                            ->maxLength(100),

                        Forms\Components\View::make('vendor_expertise_attachment_viewer')
                            ->viewData([
                                'collectionName' => 'vendor_expertise_attachment',
                                'viewLabel' => 'Expertise Attachment',
                            ])
                            ->view('filament.forms.components.attachment-viewer')
                            ->visibleOn('view'),

                        Forms\Components\SpatieMediaLibraryFileUpload::make('vendor_expertise_attachment')
                            ->collection('vendor_expertise_attachment')
                            ->maxFiles(1)
                            ->label((string) __('Expertise Attachment (PDF, max 2MB)'))
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(2048)
                            ->downloadable()
                            ->hiddenOn('view'),
                    ]),
            ]);
    }
}
