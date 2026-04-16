<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="Email Verification" />

        <h1 class="bc-title text-center">Verify your email</h1>
        <p class="bc-muted mt-2 text-center">
            We sent a link to your inbox. Click it to verify, or request a new
            email below.
        </p>

        <div
            v-if="verificationLinkSent"
            class="mt-6 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-200"
        >
            A new verification link has been sent to the email address you used
            at registration.
        </div>

        <form class="mt-8" @submit.prevent="submit">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <PrimaryButton
                    :class="{ 'opacity-40': form.processing }"
                    :disabled="form.processing"
                >
                    Resend verification email
                </PrimaryButton>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="bc-link text-center sm:text-end"
                    >Log out</Link
                >
            </div>
        </form>
    </GuestLayout>
</template>
