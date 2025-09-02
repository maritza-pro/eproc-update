<x-filament-panels::page.unsaved-data-changes-alert />
<x-filament-panels::page>
    <form wire:submit="save">
        <fieldset class="space-y-4" wire:loading.attr="disabled" wire:loading.class="opacity-50 transition"
            wire:target="save">
            {{ $this->form }}

            <div class="flex justify-end gap-3">
                <x-filament-actions::actions :actions="$this->getFormActions()" />
            </div>
        </fieldset>
    </form>

    <x-filament-actions::modals />
    </x-filament-palnes::page>