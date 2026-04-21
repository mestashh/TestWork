<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { useAuth } from '@/composables/useAuth';

const { isAuthenticated, logout } = useAuth();

const handleLogout = () => {
    logout();
    router.visit('/');
};
</script>

<template>
    <div class="min-h-screen bg-neutral-950 text-neutral-100">
        <header
            class="sticky top-0 z-40 border-b border-white/10 bg-neutral-950/90 backdrop-blur"
        >
            <div
                class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4"
            >
                <Link
                    href="/"
                    class="text-xl font-bold tracking-tight transition hover:text-white/80"
                >
                    Каталог товаров
                </Link>

                <div class="flex items-center gap-3">
                    <template v-if="isAuthenticated">
                        <Link
                            href="/admin/products"
                            class="rounded-xl border border-white/10 px-4 py-2 text-sm font-medium text-neutral-200 transition hover:border-white/20 hover:bg-white/5"
                        >
                            Управление товарами
                        </Link>

                        <button
                            class="rounded-xl border border-white/10 px-4 py-2 text-sm font-medium text-neutral-200 transition hover:border-white/20 hover:bg-white/5"
                            @click="handleLogout"
                        >
                            Выйти
                        </button>
                    </template>

                    <template v-else>
                        <Link
                            href="/login"
                            class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-black transition hover:bg-neutral-200"
                        >
                            Вход
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <main>
            <slot />
        </main>
    </div>
</template>
