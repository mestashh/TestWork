<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { useProducts } from '@/composables/useProducts';
import MainLayout from '@/layouts/MainLayout.vue';
import { getApiErrorMessage, getApiErrorStatus } from '@/utils/apiError';

type ProductShowPageProps = {
    id: number | string;
};

const page = usePage<ProductShowPageProps>();
const id = page.props.id;

const { product, loading, fetchProduct } = useProducts();

const notFound = ref(false);
const requestError = ref('');

onMounted(async () => {
    notFound.value = false;
    requestError.value = '';

    try {
        await fetchProduct(id);
    } catch (error: unknown) {
        if (getApiErrorStatus(error) === 404) {
            notFound.value = true;
            return;
        }

        requestError.value = getApiErrorMessage(
            error,
            'Не удалось загрузить товар',
        );
    }
});
</script>

<template>
    <MainLayout>
        <section class="mx-auto max-w-5xl px-6 py-10">
            <div class="mb-8 flex items-center justify-between gap-4">
                <div>
                    <p
                        class="mb-2 text-sm font-medium tracking-[0.2em] text-neutral-500 uppercase"
                    >
                        Карточка товара
                    </p>
                    <h1 class="text-4xl font-bold tracking-tight">
                        Информация о товаре
                    </h1>
                </div>

                <Link
                    href="/"
                    class="rounded-2xl border border-white/10 px-4 py-2 text-sm font-medium text-white transition hover:border-white/20 hover:bg-white/5"
                >
                    Назад к товарам
                </Link>
            </div>

            <div
                v-if="loading"
                class="rounded-2xl border border-white/10 bg-white/[0.03] p-8 text-neutral-300"
            >
                Загрузка товара...
            </div>

            <div
                v-else-if="requestError"
                class="rounded-2xl border border-red-500/20 bg-red-500/10 p-8 text-center text-red-400"
            >
                {{ requestError }}
            </div>

            <div
                v-else-if="notFound"
                class="rounded-2xl border border-dashed border-white/10 bg-white/[0.03] p-10 text-center"
            >
                <p class="text-xl font-semibold text-white">Товар не найден</p>
                <p class="mt-2 text-neutral-400">
                    Возможно, он был удален или такого товара не существует.
                </p>

                <div class="mt-6">
                    <Link
                        href="/"
                        class="inline-flex items-center rounded-2xl border border-white/10 px-5 py-3 text-sm font-medium text-white transition hover:border-white/20 hover:bg-white/5"
                    >
                        Вернуться в каталог
                    </Link>
                </div>
            </div>

            <div
                v-else-if="product"
                class="rounded-3xl border border-white/10 bg-white/[0.03] p-8 shadow-sm"
            >
                <div
                    class="mb-6 flex flex-col gap-4 md:flex-row md:items-start md:justify-between"
                >
                    <div>
                        <h2
                            class="text-4xl font-bold tracking-tight text-white"
                        >
                            {{ product.name }}
                        </h2>

                        <p class="mt-3 text-base text-neutral-400">
                            Категория:
                            <span class="text-neutral-200">
                                {{
                                    product.category_info?.name ||
                                    'Без категории'
                                }}
                            </span>
                        </p>
                    </div>

                    <div
                        class="self-start rounded-2xl bg-white/[0.05] px-5 py-3 text-2xl font-bold text-white"
                    >
                        {{ product.price }} ₽
                    </div>
                </div>

                <div
                    class="rounded-2xl border border-white/10 bg-neutral-900/50 p-6"
                >
                    <h3 class="mb-3 text-lg font-semibold text-white">
                        Описание
                    </h3>
                    <p class="leading-8 text-neutral-300">
                        {{ product.description }}
                    </p>
                </div>
            </div>
        </section>
    </MainLayout>
</template>
