<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="bc-page-bg relative">
        <div class="bc-glow" aria-hidden="true">
            <div
                class="absolute -left-32 top-0 h-[380px] w-[380px] rounded-full bg-[#2563eb]/20 blur-[100px]"
            />
            <div
                class="absolute right-0 top-1/4 h-[320px] w-[320px] rounded-full bg-[#eab308]/10 blur-[90px]"
            />
        </div>

        <div
            class="relative flex min-h-screen flex-col items-center justify-center px-4 py-10 sm:px-6"
        >
            <Link href="/" class="mb-8 flex flex-col items-center gap-2 text-center">
                <img
                    src="/images/skill-first-hiring-logo.png"
                    alt="Skill-First Hiring"
                    class="h-16 w-auto sm:h-20"
                />
                <span
                    class="text-xs font-semibold uppercase tracking-[0.2em] text-[#eab308]"
                    >BrainCode 360</span
                >
            </Link>

            <div class="bc-card w-full max-w-md px-6 py-8 sm:px-8">
                <slot />
            </div>

            <p class="mt-8 text-center text-xs text-slate-500">
                <Link href="/" class="bc-link">← Back to home</Link>
            </p>
        </div>
    </div>
</template>
