import { reactive, watch, onMounted } from 'vue';

const SORTABLE = ['id', 'name', 'birthdate', 'sex', 'salary', 'nik', 'is_active'];

export function useEmployeeList() {
    const state = reactive({
        items: [],
        meta: { current_page: 1, last_page: 1, per_page: 10, total: 0, from: 0, to: 0 },
        loading: false,
        error: null,
        search: '',
        sort: 'name',
        direction: 'asc',
    });

    let searchDebounce = null;

    async function fetchList(page = 1) {
        state.loading = true;
        state.error = null;
        try {
            const { data } = await window.axios.get('/api/employees', {
                params: {
                    page,
                    per_page: state.meta.per_page,
                    search: state.search || undefined,
                    sort: state.sort,
                    direction: state.direction,
                },
            });
            state.items = data.data;
            state.meta = data.meta;
        } catch (err) {
            state.error = err.response?.data?.message ?? err.message;
        } finally {
            state.loading = false;
        }
    }

    function changeSort(column) {
        if (!SORTABLE.includes(column)) return;
        if (state.sort === column) {
            state.direction = state.direction === 'asc' ? 'desc' : 'asc';
        } else {
            state.sort = column;
            state.direction = 'asc';
        }
        fetchList(1);
    }

    function sortIcon(column) {
        if (state.sort !== column) return '';
        return state.direction === 'asc' ? '▲' : '▼';
    }

    watch(() => state.search, () => {
        clearTimeout(searchDebounce);
        searchDebounce = setTimeout(() => fetchList(1), 300);
    });

    watch(() => state.meta.per_page, () => fetchList(1));

    onMounted(() => fetchList(1));

    return { state, fetchList, changeSort, sortIcon };
}
