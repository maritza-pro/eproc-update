<x-filament-panels::page>
    <form wire:submit="save">
        <fieldset wire:loading.attr="disabled" wire:loading.class="opacity-50 transition" wire:target="save">
            {{ $this->form }}
            <div class="flex justify-end gap-3 mt-4">
                <x-filament::button type="button" x-on:click="window.location.reload()" color="gray"
                    style="margin-top: 1rem">
                    Cancel
                </x-filament::button>

                <x-filament::button type="submit" wire:target="save" style="margin-top: 1rem">
                    <span wire:loading.remove wire:target="save">Save Changes</span>
                    <span wire:loading wire:target="save">Saving…</span>
                </x-filament::button>
            </div>
        </fieldset>
    </form>
</x-filament-panels::page>