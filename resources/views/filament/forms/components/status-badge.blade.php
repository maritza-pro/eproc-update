@php
    $status = $getRecord()->verification_status;
@endphp

@if ($status)
    <x-filament::badge :color="$status->getColor()">
        {{ $status->getLabel() }}
    </x-filament::badge>
@endif