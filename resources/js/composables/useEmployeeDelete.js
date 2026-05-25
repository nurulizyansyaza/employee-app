import { ref } from 'vue';

export function useEmployeeDelete(onDeleted) {
    const showDeleteConfirm = ref(false);
    const deletingEmployee = ref(null);
    const deleteError = ref(null);
    const deleteLoading = ref(false);

    function openDelete(employee) {
        deletingEmployee.value = employee;
        deleteError.value = null;
        showDeleteConfirm.value = true;
    }

    function closeDelete() {
        showDeleteConfirm.value = false;
        deletingEmployee.value = null;
        deleteError.value = null;
    }

    async function confirmDelete() {
        if (!deletingEmployee.value) return;
        deleteLoading.value = true;
        deleteError.value = null;
        try {
            await window.axios.delete(`/api/employees/${deletingEmployee.value.id}`);
            closeDelete();
            onDeleted?.();
        } catch (err) {
            deleteError.value = err.response?.data?.message ?? err.message;
        } finally {
            deleteLoading.value = false;
        }
    }

    return { showDeleteConfirm, deletingEmployee, deleteError, deleteLoading, openDelete, closeDelete, confirmDelete };
}
