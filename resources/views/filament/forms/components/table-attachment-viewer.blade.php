@php
    $record = $getRecord();

    $mediaItem = ($record && isset($collectionName)) ? $record->getFirstMedia($collectionName) : null;
@endphp

<div>
    @if ($mediaItem)
        <a href="{{ $mediaItem->getUrl() }}" target="_blank" wire:click.stop
            class="inline-flex items-center space-x-2 text-sm font-medium transition text-primary-600 hover:underline hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400">

            @if (str_starts_with($mediaItem->mime_type, 'image'))
                <x-heroicon-o-photo class="w-5 h-5 flex-shrink-0" />
            @else
                <x-heroicon-o-document-text class="w-5 h-5 flex-shrink-0" />
            @endif

            <span class="truncate" title="{{ $mediaItem->name }}">
                {{ $mediaItem->name }}
            </span>
        </a>
    @endif
</div>