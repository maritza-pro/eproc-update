@php
    $record = $getRecord();
    
    $mediaItem = ($record && isset($collectionName)) ? $record->getFirstMedia($collectionName) : null;
@endphp

<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
        Saved Attachment
    </label>

    @if ($mediaItem)
        <div class="p-2 border rounded-lg dark:border-white/10">
            @if (str_starts_with($mediaItem->mime_type, 'image'))
                <a href="{{ $mediaItem->getUrl() }}" target="_blank">
                    <img src="{{ $mediaItem->getUrl('thumb') ?: $mediaItem->getUrl() }}" alt="Attachment" class="max-w-xs h-auto rounded-lg shadow-md">
                </a>
            @else
                <a href="{{ $mediaItem->getUrl() }}" target="_blank" class="flex items-center space-x-3 bg-gray-50 hover:bg-gray-100 p-3 rounded-lg border">
                    <svg class="h-8 w-8 text-gray-400 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <div class="overflow-hidden">
                        <span class="text-sm font-semibold text-primary-600 truncate">{{ $mediaItem->name }}.{{ pathinfo($mediaItem->file_name, PATHINFO_EXTENSION) }}</span>
                        <span class="block text-xs text-gray-500">View/Download Attachment</span>
                    </div>
                </a>
            @endif
        </div>
    @else
        <div class="p-3 border rounded-lg text-sm text-gray-500 dark:border-white/10">
            No attachment uploaded.
        </div>
    @endif
</div>