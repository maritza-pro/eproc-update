<?php

declare(strict_types = 1);

namespace App\Filament\Resources\VendorResource\Components;

use Filament\Forms;
use Filament\Forms\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class FinancialTab
{
    public static function make(): Tab
    {
        return Tab::make('Financial')
            ->schema([
                Forms\Components\Repeater::make('bankVendors')
                    ->relationship()
                    ->hiddenLabel()
                    ->addActionLabel('Add Bank Account')
                    ->defaultItems(0)
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('bank_id')
                                    ->relationship(
                                        name: 'bank',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Builder $query): Builder => $query->where('is_active', true))
                                    ->searchable()
                                    ->preload()
                                    ->nullable()
                                    ->label('Bank Name'),
                                Forms\Components\TextInput::make('account_name')
                                    ->label('Account Name')
                                    ->nullable(),

                                Forms\Components\TextInput::make('account_number')
                                    ->label('Account Number')
                                    ->nullable(),
                                Forms\Components\View::make('recent_financial_report_attachment_viewer')
                                    ->viewData([
                                        'collectionName' => 'recent_financial_report_attachment',
                                        'viewLabel' => 'Recent Financial Report attachment',
                                    ])
                                    ->view('filament.forms.components.attachment-viewer')
                                    ->visibleOn('view'),
                                Forms\Components\SpatieMediaLibraryFileUpload::make('recent_financial_report_attachment')
                                    ->collection('recent_financial_report_attachment')
                                    ->label('Recent Financial Report (PDF, max 2MB)')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->maxSize(2048)
                                    ->maxFiles(1)
                                    ->downloadable()
                                    ->hiddenOn('view'),

                                Forms\Components\Toggle::make('is_active')
                                    ->nullable(),
                            ]),
                    ]),
            ]);
    }
}
