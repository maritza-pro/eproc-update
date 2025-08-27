<?php

declare(strict_types = 1);

namespace App\Filament\Resources\VendorResource\Components;

use Filament\Forms;
use Filament\Forms\Components\Tabs\Tab;

class CompanyInformationTab
{
    public static function make(): Tab
    {
        return Tab::make('Company Information')
            ->schema([
                Forms\Components\Group::make()
                    ->relationship(
                        'vendorProfile',
                        condition: fn (?array $state): bool => collect($state ?? [])
                            ->filter(fn ($v) => filled($v))
                            ->isNotEmpty()
                    )
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                // Forms\Components\TextInput::make('business_entity_type')
                                //     ->label('Business Entity Type')
                                //     ->nullable(),

                                Forms\Components\TextInput::make('tax_identification_number')
                                    ->label('Tax Identification Number (NPWP)')
                                    ->nullable(),

                                Forms\Components\TextInput::make('business_identification_number')
                                    ->label('Business Registration Number (NIB)')
                                    ->nullable(),

                                Forms\Components\TextInput::make('website')
                                    ->label('Website')
                                    ->url()
                                    ->nullable(),

                                Forms\Components\TextInput::make('established_year')
                                    ->label('Year Established')
                                    ->numeric()
                                    ->minValue(1900)
                                    ->maxValue(now()->year),

                                Forms\Components\TextInput::make('employee_count')
                                    ->label('Number of Employees')
                                    ->numeric()
                                    ->nullable(),
                            ]),

                        Forms\Components\Textarea::make('head_office_address')
                            ->autosize()
                            ->label('Head Office Address')
                            ->nullable(),
                    ]),
            ]);
    }
}