<script setup lang="ts">
import { router, usePage, Link } from '@inertiajs/vue3';
import { computed, onMounted, reactive, ref } from 'vue';
import ConfirmModal from '@/components/ConfirmModal.vue';
import { useProducts  } from '@/composables/useProducts';
import type {ValidationErrors} from '@/composables/useProducts';
import AdminLayout from '@/layouts/AdminLayout.vue';
import {
    getApiErrorMessage,
    getApiErrorStatus,
    getApiValidationErrors,
} from '@/utils/apiError';

type ProductFormPageProps = {
    id?: number | string | null;
};

type ProductPayload = {
    name: string;
    description: string;
    price: number;
    category_id: number | string;
};

const page = usePage<ProductFormPageProps>();
const id = page.props.id ?? null;
const isEdit = computed(() => !!id);

const form = reactive({
    name: '',
    description: '',
    price: '',
    category_id: '',
});

const validationErrors = ref<ValidationErrors>({});
const loading = ref(false);
const showSaveModal = ref(false);

const notFound = ref(false);
const requestError = ref('');

const {
    categories,
    product,
    fetchCategories,
    fetchProduct,
    createProduct,
    updateProduct,
} = useProducts();

const saveProduct = async (): Promise<void> => {
    loading.value = true;
    validationErrors.value = {};
    requestError.value = '';

    try {
        const payload: ProductPayload = {
            ...form,
            price: Number(form.price),
            category_id: form.category_id,
        };

        if (isEdit.value && id !== null) {
            await updateProduct(id, payload);
        } else {
            await createProduct(payload);
        }

        router.visit('/admin/products');
    } catch (error: unknown) {
        if (getApiErrorStatus(error) === 422) {
            validationErrors.value = getApiValidationErrors(error);
        } else if (getApiErrorStatus(error) === 404) {
            notFound.value = true;
        } else {
            requestError.value = getApiErrorMessage(error, 'Ошибка сохранения');
        }
    } finally {
        loading.value = false;
        showSaveModal.value = false;
    }
};

const submit = async (): Promise<void> => {
    if (isEdit.value) {
        showSaveModal.value = true;

        return;
    }

    await saveProduct();
};

onMounted(async () => {
    requestError.value = '';
    notFound.value = false;

    await fetchCategories();

    if (isEdit.value && id !== null) {
        try {
            await fetchProduct(id);

            if (product.value) {
                form.name = product.value.name ?? '';
                form.description = product.value.description ?? '';
                form.price = String(product.value.price ?? '');
                form.category_id = String(product.value.category_id ?? '');
            }
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
    }
});
</script>

<template>
    <AdminLayout>
        <section class="mx-auto max-w-4xl px-6 py-10">
            <div class="mb-8">
                <h1 class="text-4xl font-bold tracking-tight">
                    {{ isEdit ? 'Редактировать товар' : 'Создать товар' }}
                </h1>
                <p class="mt-2 text-neutral-400">
                    {{
                        isEdit
                            ? 'Измените данные товара и сохраните обновления.'
                            : 'Заполните форму, чтобы добавить новый товар в каталог.'
                    }}
                </p>
            </div>

            <div
                v-if="
                    loading && isEdit && !product && !notFound && !requestError
                "
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
                    Невозможно отредактировать товар, которого не существует.
                </p>

                <div class="mt-6 flex justify-center gap-3">
                    <Link
                        href="/admin/products"
                        class="rounded-2xl border border-white/10 px-5 py-3 text-sm font-medium text-white transition hover:border-white/20 hover:bg-white/5"
                    >
                        Назад к управлению
                    </Link>

                    <Link
                        href="/"
                        class="rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-black transition hover:bg-neutral-200"
                    >
                        В каталог
                    </Link>
                </div>
            </div>

            <div
                v-else
                class="rounded-2xl border border-white/10 bg-white/[0.03] p-6 shadow-sm"
            >
                <form class="space-y-6" @submit.prevent="submit">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label
                                class="mb-2 block text-sm font-medium text-neutral-200"
                            >
                                Название
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full rounded-xl border border-white/10 bg-neutral-900 px-4 py-3 text-sm text-white transition outline-none placeholder:text-neutral-500 focus:border-white/30"
                                placeholder="Введите название товара"
                            />
                            <p
                                v-if="validationErrors.name"
                                class="mt-2 text-sm text-red-400"
                            >
                                {{ validationErrors.name[0] }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-sm font-medium text-neutral-200"
                            >
                                Категория
                            </label>
                            <select
                                v-model="form.category_id"
                                class="w-full rounded-xl border border-white/10 bg-neutral-900 px-4 py-3 text-sm text-white transition outline-none focus:border-white/30"
                            >
                                <option value="">Выберите категорию</option>
                                <option
                                    v-for="category in categories"
                                    :key="category.id"
                                    :value="category.id"
                                >
                                    {{ category.name }}
                                </option>
                            </select>
                            <p
                                v-if="validationErrors.category_id"
                                class="mt-2 text-sm text-red-400"
                            >
                                {{ validationErrors.category_id[0] }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-sm font-medium text-neutral-200"
                            >
                                Цена
                            </label>
                            <input
                                v-model="form.price"
                                type="number"
                                min="1"
                                class="w-full rounded-xl border border-white/10 bg-neutral-900 px-4 py-3 text-sm text-white transition outline-none placeholder:text-neutral-500 focus:border-white/30"
                                placeholder="Введите цену"
                            />
                            <p
                                v-if="validationErrors.price"
                                class="mt-2 text-sm text-red-400"
                            >
                                {{ validationErrors.price[0] }}
                            </p>
                        </div>

                        <div class="md:col-span-2">
                            <label
                                class="mb-2 block text-sm font-medium text-neutral-200"
                            >
                                Описание
                            </label>
                            <textarea
                                v-model="form.description"
                                rows="6"
                                class="w-full rounded-xl border border-white/10 bg-neutral-900 px-4 py-3 text-sm text-white transition outline-none placeholder:text-neutral-500 focus:border-white/30"
                                placeholder="Введите описание товара"
                            />
                            <p
                                v-if="validationErrors.description"
                                class="mt-2 text-sm text-red-400"
                            >
                                {{ validationErrors.description[0] }}
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-black transition hover:bg-neutral-200 disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="loading"
                        >
                            {{
                                loading
                                    ? 'Сохранение...'
                                    : isEdit
                                      ? 'Сохранить изменения'
                                      : 'Создать товар'
                            }}
                        </button>

                        <button
                            type="button"
                            class="rounded-2xl border border-white/10 px-5 py-3 text-sm font-medium text-white transition hover:border-white/20 hover:bg-white/5"
                            @click="router.visit('/admin/products')"
                        >
                            Отмена
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <ConfirmModal
            :show="showSaveModal"
            title="Сохранение изменений"
            message="Вы уверены, что хотите сохранить изменения товара?"
            confirm-text="Сохранить"
            cancel-text="Отмена"
            @confirm="saveProduct"
            @cancel="showSaveModal = false"
        />
    </AdminLayout>
</template>
