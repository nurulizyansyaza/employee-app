<script setup>
defineProps({
    title: { type: String, required: true },
    maxWidth: { type: String, default: 'max-w-lg' },
    scrollable: { type: Boolean, default: true },
});

const emit = defineEmits(['close']);
</script>

<template>
    <div
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
        role="dialog"
        aria-modal="true"
        @click.self="emit('close')"
    >
        <div
            class="w-full bg-white rounded-md shadow-lg dark:bg-neutral-900"
            :class="[maxWidth, scrollable ? 'max-h-[90vh] overflow-y-auto' : '']"
        >
            <div class="flex items-center justify-between px-4 py-3 border-b border-neutral-200 dark:border-neutral-800">
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">{{ title }}</h3>
                <button type="button" @click="emit('close')" class="text-neutral-500 hover:text-black dark:hover:text-white">✕</button>
            </div>
            <slot />
        </div>
    </div>
</template>
