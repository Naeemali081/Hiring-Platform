<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    demoAccounts: {
        type: Array,
        default: () => [],
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Company Login" />

        <h1 class="bc-title text-center">Welcome back</h1>
        <p class="bc-muted mt-2 text-center">
            Sign in to your company dashboard.
        </p>

        <div
            v-if="status"
            class="mt-6 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-200"
        >
            {{ status }}
        </div>

        <div
            v-if="demoAccounts.length"
            class="mt-6 rounded-lg border border-[#2563eb]/30 bg-[#2563eb]/10 p-4 text-sm text-slate-200"
        >
            <p class="font-semibold text-[#93c5fd]">Demo accounts</p>
            <div
                v-for="account in demoAccounts"
                :key="account.email"
                class="mt-2 font-mono text-xs text-slate-300"
            >
                {{ account.label }}: {{ account.email }} /
                {{ account.password }}
            </div>
        </div>

        <form class="mt-8 space-y-5" @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-2 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-2 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2">
                    <Checkbox
                        name="remember"
                        v-model:checked="form.remember"
                    />
                    <span class="text-sm text-slate-400">Remember me</span>
                </label>
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="bc-link"
                >
                    Forgot password?
                </Link>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                <Link :href="route('register')" class="bc-link text-center sm:text-start">
                    Create company account
                </Link>
                <PrimaryButton
                    class="sm:ms-auto"
                    :class="{ 'opacity-40': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
