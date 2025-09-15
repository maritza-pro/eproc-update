<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Boquizo\FilamentLogViewer\Pages\ListLogs as BaseListLogs;
use Filament\Tables\Table;

class ListLogs extends BaseListLogs
{
    protected static ?string $navigationGroup = 'Monitoring';

    protected static ?string $navigationLabel = 'Application Logs';

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->defaultPaginationPageOption(25)
            ->poll('30s'); // Auto-refresh every 30 seconds
    }
}
