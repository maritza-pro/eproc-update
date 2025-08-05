@php
    $mediaItem = $getRecord()->getFirstMedia('attachment');
@endphp

@if ($mediaItem)
    <div class="p-4 border rounded-lg">
        <h3 class="text-lg font-semibold mb-2">Saved Attachment</h3>

        @if (str_starts_with($mediaItem->mime_type, 'image'))
            <a href="{{ $mediaItem->getUrl() }}" target="_blank">
                <img src="{{ $mediaItem->getUrl('thumb') ?: $mediaItem->getUrl() }}" alt="Attachment" class="max-w-xs h-auto rounded-lg shadow-md">
            </a>
        @else
            <a href="{{ $mediaItem->getUrl() }}" target="_blank" class="flex items-center space-x-3 bg-gray-50 hover:bg-gray-100 p-3 rounded-lg border">
                <svg class="h-10 w-10 text-gray-400" ...>...</svg>
                <div>
                    <span class="font-semibold text-primary-600">View/Download Attachment</span>
                    <span class="block text-sm text-gray-500">{{ $mediaItem->name }}.{{ pathinfo($mediaItem->file_name, PATHINFO_EXTENSION) }}</span>
                    </span>
                </div>
            </a>
        @endif
    </div>
@else
    <div class="p-4 border rounded-lg text-gray-500">No attachment uploaded.</div>
@endif
