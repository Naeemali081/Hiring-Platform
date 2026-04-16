<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    plans: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    name: '',
    company_name: '',
    email: '',
    role: 'company_admin',
    plan: 'free',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Company Registration" />

        <h1 class="bc-title text-center">Create your company</h1>
        <p class="bc-muted mt-2 text-center">
            Start running skill-first assessments on BrainCode 360.
        </p>

        <form class="mt-8 space-y-4" @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Full Name" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-2 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="company_name" value="Company Name" />
                <TextInput
                    id="company_name"
                    type="text"
                    class="mt-2 block w-full"
                    v-model="form.company_name"
                    required
                />
                <InputError class="mt-2" :message="form.errors.company_name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-2 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="role" value="Primary Role" />
                <select id="role" v-model="form.role" class="bc-select mt-2">
                    <option value="company_admin">Company Admin</option>
                    <option value="interviewer">Interviewer</option>
                </select>
                <InputError class="mt-2" :message="form.errors.role" />
            </div>

            <div>
                <InputLabel for="plan" value="Plan" />
                <select id="plan" v-model="form.plan" class="bc-select mt-2">
                    <option
                        v-for="plan in plans"
                        :key="plan.value"
                        :value="plan.value"
                    >
                        {{ plan.label }}
                    </option>
                </select>
                <InputError class="mt-2" :message="form.errors.plan" />
            </div>

            <div>
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-2 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
                <InputLabel
                    for="password_confirmation"
                    value="Confirm Password"
                />
                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-2 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
                <Link :href="route('login')" class="bc-link">
                    Already registered? Log in
                </Link>
                <PrimaryButton
                    class="sm:ms-auto"
                    :class="{ 'opacity-40': form.processing }"
                    :disabled="form.processing"
                >
                    Create company account
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
