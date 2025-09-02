<x-filament-panels::page.unsaved-data-changes-alert />
<x-filament-panels::page>
    @livewire(\App\Filament\Resources\VendorResource\RelationManagers\LegalityDocumentsRelationManager::class, ['ownerRecord' => $record, 'pageClass' => static::class])
    @livewire(\App\Filament\Resources\VendorResource\RelationManagers\LicensingDocumentsRelationManager::class, ['ownerRecord' => $record, 'pageClass' => static::class])
</x-filament-panels::page>