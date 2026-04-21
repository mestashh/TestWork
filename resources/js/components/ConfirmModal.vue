<script setup lang="ts">
defineProps({
    show: {
        type: Boolean,
        required: true,
    },
    title: {
        type: String,
        default: 'Подтверждение',
    },
    message: {
        type: String,
        default: 'Вы уверены?',
    },
    confirmText: {
        type: String,
        default: 'Подтвердить',
    },
    cancelText: {
        type: String,
        default: 'Отмена',
    },
    danger: {
        type: Boolean,
        default: false,
    },
});
defineEmits(['confirm', 'cancel']);
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4 backdrop-blur-sm"
    >
        <div
            class="w-full max-w-lg rounded-3xl border border-white/10 bg-neutral-950 p-8 shadow-2xl"
        >
            <div class="mb-6">
                <h2 class="text-3xl font-bold tracking-tight text-white">
                    {{ title }}
                </h2>
                <p class="mt-4 text-lg leading-8 text-neutral-300">
                    {{ message }}
                </p>
            </div>
            <div class="flex justify-end gap-3">
                <button
                    class="rounded-2xl border border-white/10 px-5 py-3 text-sm font-medium text-white transition hover:border-white/20 hover:bg-white/5"
                    @click="$emit('cancel')"
                >
                    {{ cancelText }}
                </button>

                <button
                    class="rounded-2xl px-5 py-3 text-sm font-semibold transition"
                    :class="
                        danger
                            ? 'bg-red-600 text-white hover:bg-red-500'
                            : 'bg-white text-black hover:bg-neutral-200'
                    "
                    @click="$emit('confirm')"
                >
                    {{ confirmText }}
                </button>
            </div>
        </div>
    </div>
</template>
