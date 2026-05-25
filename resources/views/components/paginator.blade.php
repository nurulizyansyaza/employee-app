@props(['data'])

<div class="flex flex-col items-center justify-center gap-2 mt-2 sm:flex-row sm:justify-between">

    @if ($data->lastPage() > 1)
        @php
            // Calculate the start and end page numbers for the pagination links
            $start = max(1, $data->currentPage() - 2);
            $end = min($start + 4, $data->lastPage());

            // Adjust start if there are fewer than 5 pages to show
            if ($end - $start + 1 < 5) {
                $start = max(1, $end - 4);
            }
        @endphp
        <div class="flex gap-2 sm:order-2">

            {{-- Go to first/previous page --}}
            @if ($data->onFirstPage())
                <x-button style="icon-outline" disabled aria-label="Go to first page.">
                    <x-icon-first></x-icon-first>
                </x-button>
                <x-button style="icon-outline" disabled aria-label="Go to previous page.">
                    <x-icon-previous></x-icon-previous>
                </x-button>
            @else
                <x-link style="icon-outline" href="{{ $data->url(1) }}" aria-label="Go to first page.">
                    <x-icon-first></x-icon-first>
                </x-link>
                <x-link style="icon-outline" href="{{ $data->previousPageUrl() }}" aria-label="Go to first page.">
                    <x-icon-previous></x-icon-previous>
                </x-link>
            @endif

            {{-- Pagination Links --}}
            @for ($i = $start; $i <= $end; $i++)
                @if ($i == $data->currentPage())
                    <x-link style="icon-primary" href="{{ $data->url($i) }}" class="block w-10 h-10 text-center"
                        aria-label="Go to page {{ $i }}.">{{ $i }}</x-link>
                @else
                    <x-link style="icon-outline" href="{{ $data->url($i) }}" class="block w-10 h-10 text-center"
                        aria-label="Go to page {{ $i }}.">{{ $i }}</x-link>
                @endif
            @endfor

            {{-- Go to last/next page --}}
            @if ($data->onLastPage())
                <x-button style="icon-outline" disabled aria-label="Go to next page.">

                </x-button>
                <x-button style="icon-outline" disabled aria-label="Go to last page.">
                    <x-icon-last></x-icon-last>
                </x-button>
            @else
                <x-link style="icon-outline" href="{{ $data->nextPageUrl() }}" aria-label="Go to last page.">
                    <x-icon-next></x-icon-next>
                </x-link>
                <x-link style="icon-outline" href="{{ $data->url($data->lastPage()) }}" aria-label="Go to last page.">
                    <x-icon-last></x-icon-last>
                </x-link>
            @endif
        </div>
    @endif

    <x-text class="sm:order-1">
        {{ __('Showing ') }}
        @if ($data->firstItem())
            <span class="font-medium">{{ $data->firstItem() }}</span>
            {!! __('to') !!}
            <span class="font-medium">{{ $data->lastItem() }}</span>
        @else
            {{ $data->count() }}
        @endif
        {!! __('of') !!}
        <span class="font-medium">{{ $data->total() }}</span>
        {!! __('results') !!}
    </x-text>
</div>
