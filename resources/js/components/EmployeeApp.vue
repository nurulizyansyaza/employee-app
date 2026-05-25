<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { useEmployeeList } from '../composables/useEmployeeList.js';
import { useEmployeeDelete } from '../composables/useEmployeeDelete.js';
import EmployeeDeleteConfirm from './EmployeeDeleteConfirm.vue';
import EmployeeForm from './EmployeeForm.vue';
import PlateOcrUpload from './PlateOcrUpload.vue';
import { formatCurrency, formatDate, formatSex } from '../helpers/format.js';

const { state, fetchList, changeSort, sortIcon } = useEmployeeList();
const { showDeleteConfirm, deletingEmployee, deleteError, deleteLoading, openDelete, closeDelete, confirmDelete } =
    useEmployeeDelete(() => fetchList(state.meta.current_page));

const showForm = ref(false);
const editing = ref(null);
const showOcr = ref(false);

const columns = [
    { key: 'id', label: 'Employee ID', sortable: true },
    { key: 'name', label: 'Name', sortable: true },
    { key: 'birthdate', label: 'Birthdate', sortable: true },
    { key: 'sex', label: 'Sex', sortable: true },
    { key: 'salary', label: 'Salary', sortable: true, align: 'right' },
    { key: 'nik', label: 'NIK', sortable: true },
    { key: 'is_active', label: 'Status', sortable: true },
    { key: 'actions', label: 'Action', sortable: false, align: 'right' },
];

function openCreate() {
    editing.value = null;
    showForm.value = true;
}

function openEdit(employee) {
    editing.value = { ...employee };
    showForm.value = true;
}

function onSaved() {
    showForm.value = false;
    fetchList(state.meta.current_page);
}

function openOcr() { showOcr.value = true; }

onMounted(() => window.addEventListener('open-ocr-scanner', openOcr));
onBeforeUnmount(() => window.removeEventListener('open-ocr-scanner', openOcr));
</script>

<template>
    <section class="space-y-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-1 gap-2">
                <input
                    v-model="state.search"
                    type="search"
                    placeholder="Search name, NIK, address, ID…"
                    class="w-full max-w-sm px-3 py-2 text-sm bg-white border rounded-md border-neutral-200 dark:border-neutral-800 dark:bg-neutral-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-black dark:focus:ring-white"
                />
                <select
                    v-model.number="state.meta.per_page"
                    class="px-6 py-2 text-sm bg-white border rounded-md border-neutral-200 dark:border-neutral-800 dark:bg-neutral-800 dark:text-white"
                >
                    <option :value="10">10</option>
                    <option :value="25">25</option>
                    <option :value="50">50</option>
                    <option :value="100">100</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button
                    type="button"
                    @click="openCreate"
                    class="px-4 py-2 text-sm font-semibold text-white bg-black border border-black rounded-md dark:bg-white dark:text-black dark:border-white hover:opacity-90"
                >
                    + New Employee
                </button>
            </div>
        </div>

        <div v-if="state.error" class="p-3 text-sm text-red-700 bg-red-50 border border-red-200 rounded-md dark:bg-red-950 dark:text-red-300 dark:border-red-900">
            {{ state.error }}
        </div>

        <div class="overflow-x-auto bg-white border rounded-md border-neutral-200 dark:border-neutral-800 dark:bg-neutral-800">
            <table class="w-full text-sm">
                <thead class="text-left bg-neutral-50 dark:bg-neutral-900">
                    <tr>
                        <th
                            v-for="col in columns"
                            :key="col.key"
                            scope="col"
                            class="px-3 py-2 font-semibold text-neutral-700 dark:text-neutral-200 whitespace-nowrap"
                            :class="col.align === 'right' ? 'text-right' : ''"
                        >
                            <button
                                v-if="col.sortable"
                                type="button"
                                @click="changeSort(col.key)"
                                class="inline-flex items-center gap-1 hover:underline"
                            >
                                {{ col.label }}
                                <span class="text-xs">{{ sortIcon(col.key) }}</span>
                            </button>
                            <span v-else>{{ col.label }}</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                    <tr v-if="state.loading">
                        <td :colspan="columns.length" class="px-3 py-6 text-center text-neutral-500">
                            Loading…
                        </td>
                    </tr>
                    <tr v-else-if="state.items.length === 0">
                        <td :colspan="columns.length" class="px-3 py-6 text-center text-neutral-500">
                            No employees found.
                        </td>
                    </tr>
                    <tr v-else v-for="emp in state.items" :key="emp.id" class="hover:bg-neutral-50 dark:hover:bg-neutral-900">
                        <td class="px-3 py-2 font-mono text-neutral-900 dark:text-neutral-100 whitespace-nowrap">{{ emp.id }}</td>
                        <td class="px-3 py-2 text-neutral-900 dark:text-neutral-100">{{ emp.name }}</td>
                        <td class="px-3 py-2 text-neutral-700 dark:text-neutral-300 whitespace-nowrap">{{ formatDate(emp.birthdate) }}</td>
                        <td class="px-3 py-2 text-neutral-700 dark:text-neutral-300">{{ formatSex(emp.sex) }}</td>
                        <td class="px-3 py-2 text-right text-neutral-900 dark:text-neutral-100 whitespace-nowrap">
                            {{ emp.salary_formatted ?? formatCurrency(emp.salary, emp.currency) }}
                        </td>
                        <td class="px-3 py-2 font-mono text-neutral-700 dark:text-neutral-300">{{ emp.nik }}</td>
                        <td class="px-3 py-2">
                            <span
                                class="inline-block px-2 py-0.5 text-xs rounded-full"
                                :class="emp.is_active
                                    ? 'bg-green-100 text-green-800 dark:bg-green-950 dark:text-green-300'
                                    : 'bg-neutral-200 text-neutral-700 dark:bg-neutral-700 dark:text-neutral-300'"
                            >
                                {{ emp.status_label ?? (emp.is_active ? 'Active' : 'Inactive') }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-right whitespace-nowrap">
                            <div class="inline-flex gap-2">
                                <button
                                    type="button"
                                    @click="openEdit(emp)"
                                    class="px-2 py-1 text-xs font-semibold text-black bg-white border rounded-md border-neutral-200 dark:bg-neutral-800 dark:text-white dark:border-neutral-700 hover:bg-neutral-100 dark:hover:bg-neutral-700"
                                >
                                    Edit
                                </button>
                                <button
                                    type="button"
                                    @click="openDelete(emp)"
                                    class="px-2 py-1 text-xs font-semibold text-red-700 bg-white border border-red-200 rounded-md dark:bg-neutral-800 dark:text-red-400 dark:border-red-900 hover:bg-red-50 dark:hover:bg-red-950"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex flex-col items-center gap-3 sm:flex-row sm:justify-between">
            <p class="text-sm text-neutral-600 dark:text-neutral-400">
                Showing <strong>{{ state.meta.from ?? 0 }}</strong> to <strong>{{ state.meta.to ?? 0 }}</strong>
                of <strong>{{ state.meta.total }}</strong> employees
            </p>
            <div class="flex gap-2">
                <button
                    type="button"
                    :disabled="state.meta.current_page <= 1 || state.loading"
                    @click="fetchList(state.meta.current_page - 1)"
                    class="px-3 py-1 text-sm bg-white border rounded-md border-neutral-200 dark:border-neutral-800 dark:bg-neutral-800 dark:text-white disabled:opacity-50"
                >
                    Prev
                </button>
                <span class="self-center text-sm text-neutral-600 dark:text-neutral-400">
                    Page {{ state.meta.current_page }} / {{ state.meta.last_page }}
                </span>
                <button
                    type="button"
                    :disabled="state.meta.current_page >= state.meta.last_page || state.loading"
                    @click="fetchList(state.meta.current_page + 1)"
                    class="px-3 py-1 text-sm bg-white border rounded-md border-neutral-200 dark:border-neutral-800 dark:bg-neutral-800 dark:text-white disabled:opacity-50"
                >
                    Next
                </button>
            </div>
        </div>

        <EmployeeForm
            v-if="showForm"
            :employee="editing"
            @close="showForm = false"
            @saved="onSaved"
        />

        <PlateOcrUpload
            v-if="showOcr"
            @close="showOcr = false"
        />

        <EmployeeDeleteConfirm
            v-if="showDeleteConfirm"
            :employee="deletingEmployee"
            :error="deleteError"
            :loading="deleteLoading"
            @close="closeDelete"
            @confirm="confirmDelete"
        />
    </section>
</template>
