<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}
        <div class="flex justify-end">
            <x-filament::button type="submit" style="margin-top: 1rem">
                Save Changes
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
