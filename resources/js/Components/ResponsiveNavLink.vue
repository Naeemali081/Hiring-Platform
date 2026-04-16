<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'block w-full border-s-4 border-[#eab308] bg-[#eab308]/10 py-2 pe-4 ps-3 text-start text-base font-semibold text-[#fef08a]'
        : 'block w-full border-s-4 border-transparent py-2 pe-4 ps-3 text-start text-base font-medium text-slate-400 transition hover:border-white/10 hover:bg-white/5 hover:text-white',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
