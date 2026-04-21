<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import ConfirmModal from '@/components/ConfirmModal.vue';
import { useProductFilters } from '@/composables/useProductFilters';
import { useProducts  } from '@/composables/useProducts';
import type {Product} from '@/composables/useProducts';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { getApiErrorMessage } from '@/utils/apiError';

const showDeleteModal = ref(false);
const productToDelete = ref<Product | null>(null);

const {
    products,
    categories,
    loading,
    pagination,
    fetchProducts,
    fetchCategories,
    deleteProduct,
} = useProducts();

const loadProducts = async (): Promise<void> => {
    await fetchProducts(
        currentPage.value,
        selectedCategory.value,
        search.value,
    );
};

const { currentPage, selectedCategory, search, goToPage } =
    useProductFilters(loadProducts);

const askDelete = (product: Product): void => {
    productToDelete.value = product;
    showDeleteModal.value = true;
};

const confirmDelete = async (): Promise<void> => {
    if (!productToDelete.value) {
        return;
    }

    try {
        await deleteProduct(productToDelete.value.id);
        await loadProducts();
    } catch (error: unknown) {
        alert(getApiErrorMessage(error, 'Ошибка удаления'));
    } finally {
        showDeleteModal.value = false;
        productToDelete.value = null;
    }
};

const cancelDelete = (): void => {
    showDeleteModal.value = false;
    productToDelete.value = null;
};

onMounted(async () => {
    await fetchCategories();
    await loadProducts();
});
</script>

<template>
    <AdminLayout>
        <section class="mx-auto max-w-7xl px-6 py-10">
            <div
                class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
            >
                <div>
                    <h1 class="text-4xl font-bold tracking-tight">
                        Управление товарами
                    </h1>
                    <p class="mt-2 text-neutral-400">
                        Поиск, фильтрация, создание, редактирование, удаление товаров.
                    </p>
                </div>

                <button
                    class="inline-flex items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-black transition hover:bg-neutral-200"
                    @click="router.visit('/admin/products/create')"
                >
                    Добавить товар
                </button>
            </div>

            <div
                class="mb-6 grid gap-3 rounded-2xl border border-white/10 bg-white/[0.03] p-4 md:grid-cols-[1fr_240px]"
            >
                <input
                    v-model="search"
                    type="text"
                    placeholder="Поиск по названию или описанию"
                    class="w-full rounded-xl border border-white/10 bg-neutral-900 px-4 py-3 text-sm text-white transition outline-none placeholder:text-neutral-500 focus:border-white/30"
                />

                <select
                    v-model="selectedCategory"
                    class="rounded-xl border border-white/10 bg-neutral-900 px-4 py-3 text-sm text-white transition outline-none focus:border-white/30"
                >
                    <option value="">Все категории</option>
                    <option
                        v-for="category in categories"
                        :key="category.id"
                        :value="category.id"
                    >
                        {{ category.name }}
                    </option>
                </select>
            </div>

            <div
                v-if="loading"
                class="rounded-2xl border border-white/10 bg-white/[0.03] p-6 text-neutral-300"
            >
                Загрузка товаров...
            </div>

            <template v-else>
                <div
                    class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.03]"
                >
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left">
                            <thead
                                class="border-b border-white/10 bg-white/[0.02]"
                            >
                                <tr>
                                    <th
                                        class="px-6 py-4 text-sm font-semibold text-white"
                                    >
                                        Название
                                    </th>
                                    <th
                                        class="px-6 py-4 text-sm font-semibold text-white"
                                    >
                                        Категория
                                    </th>
                                    <th
                                        class="px-6 py-4 text-sm font-semibold text-white"
                                    >
                                        Цена
                                    </th>
                                    <th
                                        class="px-6 py-4 text-sm font-semibold text-white"
                                    >
                                        Действия
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr
                                    v-for="product in products"
                                    :key="product.id"
                                    class="border-b border-white/5 transition hover:bg-white/[0.03]"
                                >
                                    <td class="px-6 py-4 align-middle">
                                        <div class="font-medium text-white">
                                            {{ product.name }}
                                        </div>
                                    </td>

                                    <td
                                        class="px-6 py-4 align-middle text-neutral-300"
                                    >
                                        {{
                                            product.category_info?.name ||
                                            'Без категории'
                                        }}
                                    </td>

                                    <td class="px-6 py-4 align-middle">
                                        <span
                                            class="rounded-xl bg-white/[0.04] px-3 py-1 text-sm font-semibold text-white"
                                        >
                                            {{ product.price }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 align-middle">
                                        <div class="flex flex-wrap gap-2">
                                            <button
                                                class="rounded-xl border border-white/10 px-4 py-2 text-sm font-medium text-white transition hover:border-white/20 hover:bg-white/5"
                                                @click="
                                                    router.visit(
                                                        `/admin/products/${product.id}/edit`,
                                                    )
                                                "
                                            >
                                                Редактировать
                                            </button>

                                            <button
                                                class="rounded-xl border border-red-500/20 px-4 py-2 text-sm font-medium text-red-400 transition hover:bg-red-500/10"
                                                @click="askDelete(product)"
                                            >
                                                Удалить
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div
                    v-if="!products.length"
                    class="mt-4 rounded-2xl border border-dashed border-white/10 bg-white/[0.03] p-8 text-center text-neutral-400"
                >
                    Ничего не найдено
                </div>

                <div
                    v-if="pagination"
                    class="mt-6 flex flex-wrap items-center gap-3"
                >
                    <button
                        class="rounded-xl border border-white/10 px-4 py-2 text-sm font-medium transition hover:border-white/20 hover:bg-white/5 disabled:cursor-not-allowed disabled:opacity-40"
                        :disabled="currentPage <= 1"
                        @click="goToPage(currentPage - 1)"
                    >
                        Назад
                    </button>

                    <div
                        class="rounded-xl border border-white/10 bg-white/[0.03] px-4 py-2 text-sm text-neutral-300"
                    >
                        Страница {{ pagination.current_page }} из
                        {{ pagination.last_page }}
                    </div>

                    <button
                        class="rounded-xl border border-white/10 px-4 py-2 text-sm font-medium transition hover:border-white/20 hover:bg-white/5 disabled:cursor-not-allowed disabled:opacity-40"
                        :disabled="currentPage >= pagination.last_page"
                        @click="goToPage(currentPage + 1)"
                    >
                        Вперед
                    </button>
                </div>
            </template>
        </section>

        <ConfirmModal
            :show="showDeleteModal"
            title="Удаление товара"
            :message="`Вы уверены, что хотите удалить товар '${productToDelete?.name ?? ''}'?`"
            confirm-text="Удалить"
            cancel-text="Отмена"
            :danger="true"
            @confirm="confirmDelete"
            @cancel="cancelDelete"
        />
    </AdminLayout>
</template>
