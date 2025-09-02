@php
    $record = $getRecord();

    $mediaItem = ($record && isset($collectionName)) ? $record->getFirstMedia($collectionName) : null;
@endphp

<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
        {{ $viewLabel ?? 'Saved Attachment' }}
    </label>

    @if ($mediaItem)
        <a href="{{ $mediaItem->getUrl() }}" target="_blank">
            <img src="{{ $mediaItem->getUrl('thumb') ?: $mediaItem->getUrl() }}" alt="Attachment" class="logo-square">
        </a>
    @else
        <div class="p-3 border rounded-lg text-sm text-gray-500 dark:border-white/10">
            No Logo uploaded.
        </div>
    @endif
</div>

<style>
    .logo-square {
        width: 80%;
        aspect-ratio: 1 / 1;
        object-fit: cover;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    }
</style>