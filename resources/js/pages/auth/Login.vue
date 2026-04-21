<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';
import { useAuth } from '@/composables/useAuth';
import { getApiErrorMessage } from '@/utils/apiError';

type LoginForm = {
    email: string;
    password: string;
};

const form = reactive<LoginForm>({
    email: '',
    password: '',
});

const errorMessage = ref('');
const loading = ref(false);

const { login, isAuthenticated } = useAuth();

if (isAuthenticated.value) {
    router.visit('/');
}

const submit = async (): Promise<void> => {
    errorMessage.value = '';

    if (!form.email || !form.password) {
        errorMessage.value = 'Введите email и пароль';

        return;
    }

    loading.value = true;

    try {
        await login(form);
        router.visit('/');
    } catch (error: unknown) {
        errorMessage.value = getApiErrorMessage(error, 'Ошибка авторизации');
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="min-h-screen bg-neutral-950 text-neutral-100">
        <div
            class="mx-auto flex min-h-screen max-w-6xl items-center px-6 py-10"
        >
            <div class="grid w-full gap-8 lg:grid-cols-2">
                <div class="hidden flex-col justify-center lg:flex">
                    <h1 class="text-5xl font-bold tracking-tight">
                        Вход в административную панель
                    </h1>
                </div>

                <div class="flex items-center justify-center">
                    <form
                        class="w-full max-w-md rounded-3xl border border-white/10 bg-white/[0.03] p-8 shadow-2xl"
                        @submit.prevent="submit"
                    >
                        <div class="mb-5">
                            <label
                                class="mb-2 block text-sm font-medium text-neutral-200"
                            >
                                Email
                            </label>
                            <input
                                v-model="form.email"
                                type="email"
                                class="w-full rounded-2xl border border-white/10 bg-neutral-900 px-4 py-3 text-sm text-white transition outline-none placeholder:text-neutral-500 focus:border-white/30"
                                placeholder="Введите email"
                            />
                        </div>

                        <div class="mb-5">
                            <label
                                class="mb-2 block text-sm font-medium text-neutral-200"
                            >
                                Пароль
                            </label>
                            <input
                                v-model="form.password"
                                type="password"
                                class="w-full rounded-2xl border border-white/10 bg-neutral-900 px-4 py-3 text-sm text-white transition outline-none placeholder:text-neutral-500 focus:border-white/30"
                                placeholder="Введите пароль"
                            />
                        </div>

                        <p
                            v-if="errorMessage"
                            class="mb-5 rounded-2xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-400"
                        >
                            {{ errorMessage }}
                        </p>

                        <button
                            type="submit"
                            class="w-full rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-black transition hover:bg-neutral-200 disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="loading"
                        >
                            {{ loading ? 'Входим...' : 'Войти' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
