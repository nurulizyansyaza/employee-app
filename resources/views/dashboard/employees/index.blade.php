@php
    $title = 'Employees';
@endphp

<x-layouts.dashboard>
    <x-slot:title>
        {{ $title }}
    </x-slot>

    <div id="employee-app"></div>
</x-layouts.dashboard>
