<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import { useProductFilters } from '@/composables/useProductFilters';
import { useProducts } from '@/composables/useProducts';
import MainLayout from '@/layouts/MainLayout.vue';

const {
    products,
    categories,
    loading,
    pagination,
    fetchProducts,
    fetchCategories,
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

onMounted(async () => {
    await fetchCategories();
    await loadProducts();
});
</script>

<template>
    <MainLayout>
        <section class="mx-auto max-w-6xl px-6 py-10">
            <div class="mb-8">
                <h1 class="text-4xl font-bold tracking-tight">
                    Каталог товаров
                </h1>
                <p class="mt-2 text-neutral-400">
                    Просмотр товаров и фильтрация по категориям.
                </p>
            </div>

            <div
                class="mb-8 grid gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 md:grid-cols-[1fr_240px]"
            >
                <input
                    v-model="search"
                    type="text"
                    placeholder="Поиск по названию или описанию"
                    class="w-full rounded-xl border border-white/10 bg-neutral-900 px-4 py-3 text-sm transition outline-none placeholder:text-neutral-500 focus:border-white/30"
                />

                <select
                    v-model="selectedCategory"
                    class="rounded-xl border border-white/10 bg-neutral-900 px-4 py-3 text-sm transition outline-none focus:border-white/30"
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
                class="rounded-2xl border border-white/10 bg-white/5 p-6 text-neutral-300"
            >
                Загрузка товаров...
            </div>

            <div v-else class="grid gap-5">
                <article
                    v-for="product in products"
                    :key="product.id"
                    class="group rounded-2xl border border-white/10 bg-white/[0.03] p-6 transition hover:-translate-y-0.5 hover:border-white/20 hover:bg-white/[0.05]"
                >
                    <div class="mb-3 flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-2xl font-semibold tracking-tight">
                                {{ product.name }}
                            </h2>
                            <p class="mt-1 text-sm text-neutral-400">
                                Категория:
                                {{
                                    product.category_info?.name ||
                                    'Без категории'
                                }}
                            </p>
                        </div>

                        <div
                            class="rounded-xl bg-white/5 px-4 py-2 text-lg font-bold text-white"
                        >
                            {{ product.price }} ₽
                        </div>
                    </div>

                    <p class="max-w-4xl leading-7 text-neutral-300">
                        {{ product.description }}
                    </p>

                    <div class="mt-5">
                        <Link
                            :href="`/product/${product.id}`"
                            class="inline-flex items-center rounded-xl border border-white/10 px-4 py-2 text-sm font-medium text-white transition hover:border-white/20 hover:bg-white/5"
                        >
                            Подробнее
                        </Link>
                    </div>
                </article>

                <div
                    v-if="!products.length"
                    class="rounded-2xl border border-dashed border-white/10 bg-white/[0.03] p-10 text-center text-neutral-400"
                >
                    Ничего не найдено
                </div>
            </div>

            <div v-if="pagination" class="mt-8 flex items-center gap-3">
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
        </section>
    </MainLayout>
</template>
