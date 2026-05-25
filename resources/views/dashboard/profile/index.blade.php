@php
    $title = 'Brand';
@endphp

<x-layouts.dashboard>
    <x-slot:title>
        {{ $title }}
    </x-slot>

    <div class="space-y-4">
        <div class="flex items-end justify-between">
            <x-subtitle>{{ __('All brand records') }}</x-subtitle>
            <x-link href="{{ route('dashboard.brand.create') }}" style="primary">{{ __('Add new record') }}</x-link>
        </div>
        <div class="overflow-hidden border rounded-md border-neutral-200 dark:border-neutral-800 ">
            <div class="overflow-x-auto">
                <table class="w-full divide-y whitespace-nowrap divide-neutral-200 dark:divide-neutral-800">
                    <thead class=" bg-neutral-50 dark:bg-neutral-900">
                        <tr>
                            <th class="px-4 py-3 text-sm text-left text-neutral-800 dark:text-neutral-200">
                                No
                            </th>
                            <th class="px-4 py-3 text-sm text-left text-neutral-800 dark:text-neutral-200">
                                Name
                            </th>
                            <th class="py-3 pr-12 text-sm text-right text-neutral-800 dark:text-neutral-200">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                        @foreach ($brands as $index => $brand)
                            <tr>
                                <td class="px-4 py-3 text-sm text-neutral-800 dark:text-neutral-200">
                                    {{ ++$index }}
                                </td>
                                <td class="px-4 py-3 text-sm text-neutral-800 dark:text-neutral-200">
                                    {{ $brand->name }}
                                </td>
                                <td
                                    class="flex justify-end gap-2 px-4 py-3 text-sm text-neutral-800 dark:text-neutral-200">
                                    <x-link href="">{{ __('View') }}</x-link>
                                    <x-link href="">{{ __('Edit') }}</x-link>
                                    <x-link href="">{{ __('Delete') }}</x-link>
                                    {{-- <x-link aria-label="View record">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" width="24"
                                            height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                            <path
                                                d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                            <path d="M11 14h1v4h1" />
                                            <path d="M12 11h.01" />
                                        </svg>
                                    </x-link>
                                    <x-link aria-label="Edit record">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" width="24"
                                            height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                            <path
                                                d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                            <path d="M10 18l5 -5a1.414 1.414 0 0 0 -2 -2l-5 5v2h2z" />
                                        </svg>
                                    </x-link>
                                    <x-button style="link" aria-label="Remove record"><svg xmlns="http://www.w3.org/2000/svg"
                                            class="w-5 h-5" width="24" height="24" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                            <path
                                                d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                            <path d="M10 12l4 4m0 -4l-4 4" />
                                        </svg>
                                    </x-button> --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-layouts.dashboard>
