<script setup lang="ts">
import { router, Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import { useAuth } from '@/composables/useAuth';

const { logout, isAuthenticated } = useAuth();

onMounted(() => {
    if (!isAuthenticated.value) {
        router.visit('/login');
    }
});

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
                class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4"
            >
                <Link
                    href="/admin/products"
                    class="text-xl font-bold tracking-tight"
                >
                    Управление товарами
                </Link>

                <div class="flex items-center gap-3">
                    <Link
                        href="/"
                        class="rounded-xl border border-white/10 px-4 py-2 text-sm font-medium transition hover:border-white/20 hover:bg-white/5"
                    >
                        Все товары
                    </Link>

                    <button
                        v-if="isAuthenticated"
                        class="rounded-xl border border-white/10 px-4 py-2 text-sm font-medium transition hover:border-white/20 hover:bg-white/5"
                        @click="handleLogout"
                    >
                        Выйти
                    </button>
                </div>
            </div>
        </header>

        <main>
            <slot />
        </main>
    </div>
</template>
