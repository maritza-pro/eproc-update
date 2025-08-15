<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}
        <div style="float:right">
            <x-filament::button type="submit" style="margin-top:15px">
                Save Changes
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
