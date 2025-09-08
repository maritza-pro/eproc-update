<?php

declare(strict_types = 1);

namespace App\Filament\Pages;

use Boquizo\FilamentLogViewer\Pages\ViewLog as BaseViewLog;
use Filament\Actions\Action;

class ViewLog extends BaseViewLog
{
    /**
     * Get the header actions.
     *
     * Defines custom actions to be displayed in the page header.
     */
    public function getHeaderActions(): array
    {
        return array_merge(parent::getHeaderActions(), [
            Action::make('export')
                ->label((string) __('Export to CSV'))
                ->icon('heroicon-o-arrow-down-tray'),
        ]);
    }
}
