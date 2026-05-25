<script setup>
import { reactive, ref, computed } from 'vue';
import AppModal from './AppModal.vue';
import FormField from './FormField.vue';

const props = defineProps({
    employee: { type: Object, default: null },
});
const emit = defineEmits(['close', 'saved']);

const isEdit = computed(() => !!props.employee?.id);

const CURRENCIES = ['USD', 'EUR', 'GBP', 'JPY', 'CNY', 'IDR', 'SGD', 'MYR', 'INR', 'AUD'];

const form = reactive({
    name: props.employee?.name ?? '',
    birthdate: props.employee?.birthdate ?? '',
    sex: props.employee ? !!props.employee.sex : true,
    address: props.employee?.address ?? '',
    salary: props.employee?.salary ?? 0,
    currency: (props.employee?.currency ?? 'USD').toString().toUpperCase(),
    nik: props.employee?.nik ?? '',
    is_active: props.employee ? !!props.employee.is_active : true,
});

const saving = ref(false);
const errors = ref({});
const generalError = ref(null);

async function submit() {
    saving.value = true;
    errors.value = {};
    generalError.value = null;
    try {
        const payload = {
            name: form.name,
            birthdate: form.birthdate,
            sex: form.sex,
            address: form.address,
            salary: form.salary,
            currency: form.currency,
            is_active: form.is_active,
        };
        if (isEdit.value) {
            await window.axios.put(`/api/employees/${props.employee.id}`, payload);
        } else {
            await window.axios.post('/api/employees', { ...payload, nik: form.nik });
        }
        emit('saved');
    } catch (err) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors ?? {};
            generalError.value = err.response.data.message ?? 'Validation failed.';
        } else {
            generalError.value = err.response?.data?.message ?? err.message;
        }
    } finally {
        saving.value = false;
    }
}

function fieldError(key) {
    return errors.value?.[key]?.[0] ?? null;
}
</script>

<template>
    <AppModal
        :title="isEdit ? `Edit Employee: ${props.employee.id}` : 'New Employee'"
        @close="emit('close')"
    >
        <form @submit.prevent="submit" class="px-4 py-4 space-y-3">
            <div v-if="generalError" class="p-2 text-sm text-red-700 bg-red-50 border border-red-200 rounded-md dark:bg-red-950 dark:text-red-300 dark:border-red-900">
                {{ generalError }}
            </div>

            <FormField label="Name" :error="fieldError('name')">
                <input
                    v-model="form.name"
                    type="text"
                    class="block w-full px-3 py-2 mt-1 text-sm bg-white border rounded-md border-neutral-200 dark:border-neutral-800 dark:bg-neutral-800 dark:text-white"
                />
            </FormField>

            <FormField label="NIK" :error="fieldError('nik')" :hint="isEdit ? 'NIK cannot be changed.' : null">
                <input
                    v-model="form.nik"
                    type="text"
                    :disabled="isEdit"
                    class="block w-full px-3 py-2 mt-1 font-mono text-sm bg-white border rounded-md border-neutral-200 dark:border-neutral-800 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                />
            </FormField>

            <div class="grid grid-cols-2 gap-3">
                <FormField label="Birthdate" :error="fieldError('birthdate')">
                    <input
                        v-model="form.birthdate"
                        type="date"
                        class="block w-full px-3 py-2 mt-1 text-sm bg-white border rounded-md border-neutral-200 dark:border-neutral-800 dark:bg-neutral-800 dark:text-white"
                    />
                </FormField>
                <FormField label="Sex">
                    <select
                        v-model="form.sex"
                        class="block w-full px-3 py-2 mt-1 text-sm bg-white border rounded-md border-neutral-200 dark:border-neutral-800 dark:bg-neutral-800 dark:text-white"
                    >
                        <option :value="true">Male</option>
                        <option :value="false">Female</option>
                    </select>
                </FormField>
            </div>

            <FormField label="Address" :error="fieldError('address')">
                <textarea
                    v-model="form.address"
                    rows="2"
                    class="block w-full px-3 py-2 mt-1 text-sm bg-white border rounded-md border-neutral-200 dark:border-neutral-800 dark:bg-neutral-800 dark:text-white"
                ></textarea>
            </FormField>

            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <FormField label="Salary" :error="fieldError('salary')">
                        <input
                            v-model.number="form.salary"
                            type="number"
                            step="0.01"
                            class="block w-full px-3 py-2 mt-1 text-sm bg-white border rounded-md border-neutral-200 dark:border-neutral-800 dark:bg-neutral-800 dark:text-white"
                        />
                    </FormField>
                </div>
                <FormField label="Currency" :error="fieldError('currency')">
                    <select
                        v-model="form.currency"
                        class="block w-full px-3 py-2 mt-1 text-sm bg-white border rounded-md border-neutral-200 dark:border-neutral-800 dark:bg-neutral-800 dark:text-white"
                    >
                        <option v-for="code in CURRENCIES" :key="code" :value="code">{{ code }}</option>
                    </select>
                </FormField>
            </div>

            <label class="inline-flex items-center gap-2 text-sm text-neutral-700 dark:text-neutral-300">
                <input v-model="form.is_active" type="checkbox" class="rounded border-neutral-300 dark:border-neutral-700" />
                Active
            </label>

            <div class="flex justify-end gap-2 pt-3 border-t border-neutral-200 dark:border-neutral-800">
                <button type="button" @click="emit('close')" class="px-4 py-2 text-sm bg-white border rounded-md border-neutral-200 dark:bg-neutral-800 dark:text-white dark:border-neutral-700">
                    Cancel
                </button>
                <button
                    type="submit"
                    :disabled="saving"
                    class="px-4 py-2 text-sm font-semibold text-white bg-black border border-black rounded-md dark:bg-white dark:text-black dark:border-white disabled:opacity-50"
                >
                    {{ saving ? 'Saving…' : (isEdit ? 'Update' : 'Create') }}
                </button>
            </div>
        </form>
    </AppModal>
</template>
