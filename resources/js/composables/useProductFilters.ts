import { ref, watch } from 'vue';

type LoadProductsFn = () => Promise<void>;

export function useProductFilters(loadProducts: LoadProductsFn) {
    const currentPage = ref(1);
    const selectedCategory = ref<string | number>('');
    const search = ref('');
    const debounceTimer = ref<ReturnType<typeof setTimeout> | null>(null);

    watch(selectedCategory, async () => {
        currentPage.value = 1;
        await loadProducts();
    });

    watch(search, () => {
        currentPage.value = 1;

        if (debounceTimer.value) {
            clearTimeout(debounceTimer.value);
        }

        debounceTimer.value = setTimeout(async () => {
            await loadProducts();
        }, 400);
    });

    const goToPage = async (page: number): Promise<void> => {
        currentPage.value = page;
        await loadProducts();
    };

    return {
        currentPage,
        selectedCategory,
        search,
        goToPage,
    };
}
