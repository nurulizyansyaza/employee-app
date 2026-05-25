<script setup>
import AppModal from './AppModal.vue';

defineProps({
    employee: { type: Object, default: null },
    error: { type: String, default: null },
    loading: { type: Boolean, default: false },
});

const emit = defineEmits(['close', 'confirm']);
</script>

<template>
    <AppModal title="Delete Employee" max-width="max-w-sm" :scrollable="false" @close="emit('close')">
        <div class="px-4 py-4 space-y-3">
            <div v-if="error" class="p-2 text-sm text-red-700 bg-red-50 border border-red-200 rounded-md dark:bg-red-950 dark:text-red-300 dark:border-red-900">
                {{ error }}
            </div>
            <p class="text-sm text-neutral-700 dark:text-neutral-300">
                Are you sure you want to delete
                <strong>{{ employee?.name }}</strong>
                <span class="font-mono">({{ employee?.id }})</span>?
                This action cannot be undone.
            </p>
            <div class="flex justify-end gap-2 pt-3 border-t border-neutral-200 dark:border-neutral-800">
                <button
                    type="button"
                    @click="emit('close')"
                    class="px-4 py-2 text-sm bg-white border rounded-md border-neutral-200 dark:bg-neutral-800 dark:text-white dark:border-neutral-700"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    :disabled="loading"
                    @click="emit('confirm')"
                    class="px-4 py-2 text-sm font-semibold text-white bg-red-600 border border-red-600 rounded-md hover:bg-red-700 disabled:opacity-50"
                >
                    {{ loading ? 'Deleting…' : 'Delete' }}
                </button>
            </div>
        </div>
    </AppModal>
</template>
