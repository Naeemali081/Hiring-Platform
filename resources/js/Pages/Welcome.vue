<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});

const steps = [
    {
        title: 'Create the assessment',
        body: 'Pick role level, choose from your library or curated problems, set time limits, and invite the candidate by email with a secure one-time link.',
    },
    {
        title: 'Candidate builds in a real environment',
        body: 'They work in a proper editor with AI allowed where you define it—while activity like typing, paste events, and focus changes is captured for transparency.',
    },
    {
        title: 'Review with evidence',
        body: 'See manual vs pasted breakdown, tab switches, execution output, and time used versus limit—then score, note, and schedule a single defend round.',
    },
];

const pillars = [
    {
        title: 'Skill-first, not trivia',
        body: 'Assessments mirror real work: APIs, frontends, queues, and design—not memorised puzzle tricks.',
    },
    {
        title: 'Transparent by design',
        body: 'Reports surface how the solution was produced so your team can trust the signal, not guess.',
    },
    {
        title: 'One defend round',
        body: 'After submission, a focused live conversation validates thinking and closes the loop for both sides.',
    },
];
</script>

<template>
    <Head title="BrainCode 360 — Skill-First Hiring" />

    <div class="min-h-screen bg-navy text-gray-2 antialiased selection:bg-purple/40 selection:text-white relative overflow-hidden">
        <!-- ambient glow matching app.css -->
        <div class="pointer-events-none fixed inset-0 overflow-hidden z-[-1]" aria-hidden="true">
            <div class="absolute -left-[10%] top-[-10%] h-[500px] w-[500px] rounded-full bg-purple-glow/10 blur-[120px] animate-blob" />
            <div class="absolute right-[-10%] top-1/3 h-[400px] w-[400px] rounded-full bg-navy-3/80 blur-[100px] animate-blob animation-delay-2000" />
            <div class="absolute bottom-[-10%] left-1/3 h-[400px] w-[400px] rounded-full bg-purple/10 blur-[100px]" />
        </div>

        <div class="relative z-10">
            <!-- nav -->
            <header class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-6 py-8">
                <div class="flex items-center gap-3">
                    <img src="/images/logo.png" alt="BrainCode 360" class="h-10 w-10 object-contain drop-shadow-[0_0_15px_rgba(124,110,250,0.5)]" />
                    <div class="hidden sm:block">
                        <p class="text-[13px] font-bold uppercase tracking-[0.2em] text-purple-glow">
                            BrainCode 360
                        </p>
                        <p class="text-[11px] text-gray-3 mt-0.5 tracking-wider uppercase font-semibold">
                            Hiring for the AI era
                        </p>
                    </div>
                </div>

                <nav v-if="canLogin" class="flex flex-shrink-0 items-center gap-4">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="btn btn-primary"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="text-sm font-semibold text-gray-3 transition hover:text-white"
                        >
                            Log in
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="btn btn-primary"
                        >
                            Register company
                        </Link>
                    </template>
                </nav>
            </header>

            <main>
                <!-- hero -->
                <section class="mx-auto max-w-6xl px-6 pb-20 pt-8 sm:pb-24 sm:pt-12">
                    <div class="grid items-center gap-12 lg:grid-cols-[1.1fr_0.9fr]">
                        <div class="animate-fade-in-up">
                            <p class="inline-flex items-center gap-2 rounded-full border border-purple/30 bg-purple/10 px-3 py-1.5 text-xs font-bold text-purple-3 backdrop-blur-sm shadow-[0_0_15px_rgba(124,110,250,0.1)]">
                                <span class="h-1.5 w-1.5 rounded-full bg-purple-glow shadow-[0_0_8px_#8A78FF]" />
                                Skill-first technical hiring platform
                            </p>
                            <h1 class="mt-6 text-4xl font-display font-extrabold leading-tight tracking-tight text-white sm:text-5xl lg:text-[3.5rem]">
                                Hire on
                                <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple to-purple-glow drop-shadow-[0_0_10px_rgba(124,110,250,0.3)]">real ability</span>
                                <br/>not memory tests.
                            </h1>
                            <p class="mt-6 max-w-xl text-lg leading-relaxed text-gray-4">
                                BrainCode 360 replaces long multi-round loops with realistic assessments, AI-transparent reporting, and a single defend round—so you decide faster and candidates are treated fairly.
                            </p>
                            <div class="mt-8 flex flex-wrap items-center gap-4">
                                <Link
                                    v-if="canRegister"
                                    :href="route('register')"
                                    class="btn btn-primary px-8 py-3.5 text-base"
                                >
                                    Get started — register company
                                </Link>
                                <Link
                                    v-if="canLogin && !$page.props.auth.user"
                                    :href="route('login')"
                                    class="btn btn-ghost px-8 py-3.5 text-base bg-surface border-surface-border text-white"
                                >
                                    Sign in to dashboard
                                </Link>
                            </div>
                        </div>

                        <div class="relative rounded-2xl border border-surface-border bg-surface backdrop-blur-xl p-8 shadow-[0_20px_50px_rgba(0,0,0,0.3)] animate-fade-in-up" style="animation-delay: 0.1s">
                            <div class="absolute -right-4 -top-4 h-32 w-32 rounded-full bg-purple-glow/20 blur-2xl" />
                            <img src="/images/logo.png" alt="BrainCode Icon" class="mx-auto h-32 w-32 object-contain drop-shadow-[0_0_20px_rgba(124,110,250,0.4)]" />
                            
                            <ul class="mt-8 space-y-5 text-sm text-gray-3">
                                <li class="flex gap-4 items-start">
                                    <span class="mt-0.5 flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-purple/20 text-xs font-bold text-purple-3 border border-purple/30">1</span>
                                    <span><strong class="text-white">Problems &amp; library</strong> — junior, mid, and senior tracks with tags and suggested times.</span>
                                </li>
                                <li class="flex gap-4 items-start">
                                    <span class="mt-0.5 flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-purple/20 text-xs font-bold text-purple-3 border border-purple/30">2</span>
                                    <span><strong class="text-white">Assessments</strong> — one link per candidate, expiry, and status from pending to reviewed.</span>
                                </li>
                                <li class="flex gap-4 items-start">
                                    <span class="mt-0.5 flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-purple/20 text-xs font-bold text-purple-3 border border-purple/30">3</span>
                                    <span><strong class="text-white">Reports</strong> — transparency metrics, execution snapshot, and defend outcome.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- pillars -->
                <section class="border-y border-surface-border bg-surface/50 py-20 backdrop-blur-sm relative z-20">
                    <div class="mx-auto max-w-6xl px-6 lg:px-8">
                        <h2 class="text-center font-display text-3xl font-bold text-white sm:text-4xl">
                            Built for how teams actually ship software
                        </h2>
                        <p class="mx-auto mt-4 max-w-2xl text-center text-gray-4 text-lg">
                            The product direction matches your internal spec: fewer rounds, clearer evidence, and room for responsible AI use where you allow it.
                        </p>
                        <div class="mt-16 grid gap-6 md:grid-cols-3">
                            <article v-for="item in pillars" :key="item.title" class="stat-card hover:border-purple/40">
                                <h3 class="text-xl font-bold text-purple-2">
                                    {{ item.title }}
                                </h3>
                                <p class="mt-3 text-sm leading-relaxed text-gray-3">
                                    {{ item.body }}
                                </p>
                            </article>
                        </div>
                    </div>
                </section>

                <!-- how it works -->
                <section class="mx-auto max-w-6xl px-6 py-20 sm:px-8 relative z-20">
                    <h2 class="font-display text-3xl font-bold text-white sm:text-4xl">
                        How it works
                    </h2>
                    <p class="mt-3 max-w-2xl text-gray-4 text-lg">
                        A straight path from problem selection to defend—without losing the detail your interviewers need.
                    </p>
                    <ol class="mt-12 space-y-6">
                        <li v-for="(step, i) in steps" :key="step.title" class="flex flex-col sm:flex-row gap-6 lg:gap-8 rounded-[20px] border border-surface-border bg-surface backdrop-blur-xl p-8 transition-all hover:bg-surface-elevated/20">
                            <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-purple to-purple-glow text-xl font-extrabold text-white shadow-[0_0_20px_rgba(124,110,250,0.3)]">
                                {{ i + 1 }}
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white">
                                    {{ step.title }}
                                </h3>
                                <p class="mt-2 text-base text-gray-4 leading-relaxed">
                                    {{ step.body }}
                                </p>
                            </div>
                        </li>
                    </ol>
                </section>

                <!-- CTA -->
                <section class="border-t border-surface-border bg-gradient-to-b from-surface/50 to-navy py-24 relative z-20 overflow-hidden">
                    <div class="absolute top-0 right-1/4 w-96 h-96 bg-purple-glow/20 rounded-full blur-[100px] pointer-events-none" />
                    
                    <div class="mx-auto max-w-6xl px-6 text-center relative z-10">
                        <h2 class="font-display text-3xl font-bold text-white sm:text-4xl shadow-black drop-shadow-md">
                            Ready to run your first assessment?
                        </h2>
                        <p class="mx-auto mt-4 max-w-xl text-gray-3 text-lg">
                            Register your company, add or pick problems, then send a candidate link and track everything from the dashboard and reports.
                        </p>
                        <div class="mt-10 flex flex-wrap justify-center gap-4">
                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="btn btn-primary px-8 py-3.5 text-base text-lg shadow-[0_10px_30px_rgba(124,110,250,0.4)]"
                            >
                                Create company account
                            </Link>
                            <Link
                                v-if="canLogin && !$page.props.auth.user"
                                :href="route('login')"
                                class="btn btn-ghost bg-surface-elevated px-8 py-3.5 text-base text-lg"
                            >
                                Already registered? Log in
                            </Link>
                        </div>
                    </div>
                </section>
            </main>

            <footer class="border-t border-surface-border py-8 text-center text-xs text-gray-4">
                <p>
                    © {{ new Date().getFullYear() }} BrainCode 360 · Skill-First Hiring Platform
                </p>
            </footer>
        </div>
    </div>
</template>
