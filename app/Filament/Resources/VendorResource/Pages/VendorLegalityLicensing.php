<?php

declare(strict_types=1);

namespace App\Filament\Resources\VendorResource\Pages;

use App\Filament\Resources\VendorResource;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\HasUnsavedDataChangesAlert;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class VendorLegalityLicensing extends Page implements HasForms
{
    use HasUnsavedDataChangesAlert, InteractsWithForms, InteractsWithRecord;

    protected static string $resource = VendorResource::class;

    protected static ?string $title = 'Legality & Licensing';

    protected static string $view = 'filament.resources.vendor-resource.pages.vendor-legality-licensing';

    public ?array $data = [];

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->form->fill($this->record->attributesToArray());
    }

    public function save(): void
    {
        $data = $this->form->getState();

        /** @var \App\Models\Vendor $vendor */
        $vendor = $this->getRecord();

        $vendor->fill($data);
        $vendor->save();

        Notification::make()
            ->title((string) __('Legality & Licensing updated successfully'))
            ->success()
            ->send();
    }

    public static function getNavigationLabel(): string
    {
        return 'Legality & Licensing';
    }
}
