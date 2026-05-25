<script setup>
import { ref } from 'vue';
import AppModal from './AppModal.vue';

const emit = defineEmits(['close']);

const file = ref(null);
const preview = ref(null);
const loading = ref(false);
const result = ref(null);
const error = ref(null);

function onFileChange(event) {
    error.value = null;
    result.value = null;
    const f = event.target.files?.[0] ?? null;
    file.value = f;
    if (f) {
        const reader = new FileReader();
        reader.onload = (e) => { preview.value = e.target?.result ?? null; };
        reader.readAsDataURL(f);
    } else {
        preview.value = null;
    }
}

async function submit() {
    if (!file.value) return;
    loading.value = true;
    error.value = null;
    result.value = null;
    try {
        const data = new FormData();
        data.append('image', file.value);
        const { data: body } = await window.axios.post('/api/employees/ocr/plate', data, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });
        result.value = body;
    } catch (err) {
        if (err.response?.status === 422) {
            const d = err.response.data;
            result.value = {
                plate_text: null,
                matches_format: false,
                raw_text: d.raw_text ?? null,
                normalized: d.normalized ?? null,
                provider: d.provider ?? null,
            };
            error.value = d.message ?? 'No valid plate detected.';
        } else if (err.response?.status === 429) {
            error.value = 'Rate limit reached (5 per minute). Please wait.';
        } else {
            error.value = err.response?.data?.message ?? err.message;
        }
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <AppModal title="Car Plate OCR" @close="emit('close')">
        <div class="px-4 py-4 space-y-3">
                <p class="text-sm text-neutral-600 dark:text-neutral-400">
                    Upload a car license plate image (JPG/PNG, max 5MB). The detector accepts plates from any country &mdash; uppercase letters and digits, 2&ndash;15 characters, with at least one letter and one digit (e.g.
                    <code class="px-1 py-0.5 text-xs rounded bg-neutral-100 dark:bg-neutral-800">B 1234 CD</code>,
                    <code class="px-1 py-0.5 text-xs rounded bg-neutral-100 dark:bg-neutral-800">ABC 1234</code>,
                    <code class="px-1 py-0.5 text-xs rounded bg-neutral-100 dark:bg-neutral-800">AB12 CDE</code>).
                </p>

                <input
                    type="file"
                    accept="image/png,image/jpeg"
                    @change="onFileChange"
                    class="block w-full text-sm text-neutral-700 dark:text-neutral-300 file:mr-3 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:border file:rounded-md file:border-neutral-200 dark:file:border-neutral-700 file:bg-white dark:file:bg-neutral-800 file:text-black dark:file:text-white"
                />

                <div v-if="preview" class="overflow-hidden border rounded-md border-neutral-200 dark:border-neutral-800">
                    <img :src="preview" alt="Preview" class="w-full max-h-64 object-contain bg-neutral-50 dark:bg-neutral-950" />
                </div>

                <button
                    type="button"
                    :disabled="!file || loading"
                    @click="submit"
                    class="w-full px-4 py-2 text-sm font-semibold text-white bg-black border border-black rounded-md dark:bg-white dark:text-black dark:border-white disabled:opacity-50"
                >
                    {{ loading ? 'Reading…' : 'Detect Plate' }}
                </button>

                <div v-if="error" class="p-2 text-sm text-red-700 bg-red-50 border border-red-200 rounded-md dark:bg-red-950 dark:text-red-300 dark:border-red-900">
                    {{ error }}
                </div>

                <div v-if="result" class="p-3 space-y-2 text-sm border rounded-md border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-950">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-neutral-900 dark:text-white">Plate:</span>
                        <span class="font-mono text-lg text-neutral-900 dark:text-white">{{ result.plate_text ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-neutral-700 dark:text-neutral-300">Format:</span>
                        <span
                            class="px-2 py-0.5 text-xs rounded-full"
                            :class="result.matches_format
                                ? 'bg-green-100 text-green-800 dark:bg-green-950 dark:text-green-300'
                                : 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300'"
                        >
                            {{ result.matches_format ? 'Valid plate format' : 'Invalid' }}
                        </span>
                    </div>
                    <div v-if="result.confidence != null" class="flex items-center justify-between">
                        <span class="text-neutral-700 dark:text-neutral-300">Confidence:</span>
                        <span class="text-neutral-900 dark:text-white">{{ (result.confidence * 100).toFixed(1) }}%</span>
                    </div>
                    <div v-if="result.raw_text" class="text-xs">
                        <span class="text-neutral-500">Raw text:</span>
                        <pre class="p-2 mt-1 overflow-x-auto whitespace-pre-wrap bg-white border rounded border-neutral-200 dark:bg-neutral-900 dark:border-neutral-800 dark:text-neutral-300">{{ result.raw_text }}</pre>
                    </div>
                    <div v-if="result.provider" class="text-xs text-neutral-500">
                        Provider: <code>{{ result.provider }}</code>
                    </div>
                </div>
        </div>
    </AppModal>
</template>
