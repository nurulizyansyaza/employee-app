@props(['status'])

@if ($status)
    <p class="text-sm text-green-600 dark:text-green-400">
        {{ $status }}
    </p>
@endif
