<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center rounded-lg border border-white/15 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 shadow-sm transition hover:bg-white/10 hover:text-white focus:outline-none focus:ring-2 focus:ring-[#2563eb] focus:ring-offset-2 focus:ring-offset-[#0c0f14] disabled:opacity-25"
    >
        <slot />
    </button>
</template>
