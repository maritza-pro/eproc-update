<?php

namespace App\Filament\Pages;

use Boquizo\FilamentLogViewer\Pages\ViewLog as BaseViewLog;
use Filament\Actions\Action;

class ViewLog extends BaseViewLog
{
    public function getHeaderActions(): array
    {
        return array_merge(parent::getHeaderActions(), [
            Action::make('export')
                ->label('Export to CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => $this->exportToCsv()),
        ]);
    }
}
