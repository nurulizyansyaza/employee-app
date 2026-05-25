<script setup>
defineProps({
    label: { type: String, required: true },
    error: { type: String, default: null },
    hint: { type: String, default: null },
});
</script>

<template>
    <div>
        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ label }}</label>
        <slot />
        <p v-if="hint" class="mt-1 text-xs text-neutral-500">{{ hint }}</p>
        <p v-if="error" class="mt-1 text-xs text-red-600">{{ error }}</p>
    </div>
</template>
