<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    assessment: {
        type: Object,
        required: true,
    },
    activity: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    status: props.assessment.status,
    defend_scheduled_at: props.assessment.defend_scheduled_at
        ? props.assessment.defend_scheduled_at.slice(0, 16)
        : '',
    score: props.assessment.submission?.score ?? '',
    reviewer_notes: props.assessment.submission?.reviewer_notes ?? '',
    defend_outcome: props.assessment.submission?.defend_outcome ?? '',
});

const submit = () => {
    form.patch(route('assessments.update', props.assessment.id));
};
</script>

<template>
    <Head :title="`Assessment - ${assessment.candidate_name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div
                class="flex flex-wrap items-center justify-between gap-4"
            >
                <h2 class="text-xl font-semibold text-white">
                    Assessment detail
                </h2>
                <Link
                    v-if="assessment.submission"
                    :href="route('reports.show', assessment.id)"
                    class="text-sm font-semibold text-[#eab308] hover:text-[#facc15]"
                >
                    Open report →
                </Link>
            </div>
        </template>

        <div class="py-10">
            <div
                class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:grid-cols-3 lg:px-8"
            >
                <div class="bc-card p-6 lg:col-span-2">
                    <h3 class="text-lg font-semibold text-white">
                        Candidate & problem
                    </h3>
                    <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-slate-500">Candidate</p>
                            <p class="font-medium text-white">
                                {{ assessment.candidate_name }}
                            </p>
                        </div>
                        <div>
                            <p class="text-slate-500">Email</p>
                            <p class="font-medium text-white">
                                {{ assessment.candidate_email }}
                            </p>
                        </div>
                        <div>
                            <p class="text-slate-500">Problem</p>
                            <p class="font-medium text-white">
                                {{ assessment.problem?.title }}
                            </p>
                        </div>
                        <div>
                            <p class="text-slate-500">Role</p>
                            <p class="font-medium text-white">
                                {{ assessment.role_level }}
                            </p>
                        </div>
                        <div>
                            <p class="text-slate-500">Time limit</p>
                            <p class="font-medium text-white">
                                {{ assessment.time_limit_minutes }} minutes
                            </p>
                        </div>
                        <div>
                            <p class="text-slate-500">Status</p>
                            <p class="font-medium text-[#eab308]">
                                {{ assessment.status }}
                            </p>
                        </div>
                    </div>

                    <div v-if="assessment.submission" class="mt-8">
                        <h4 class="text-base font-semibold text-white">
                            Submission signals
                        </h4>
                        <div class="mt-4 grid grid-cols-2 gap-4">
                            <div
                                class="rounded-lg border border-white/10 bg-[#0c0f14]/50 p-4"
                            >
                                <p class="text-sm text-slate-500">
                                    Manual typing
                                </p>
                                <p class="mt-1 text-2xl font-semibold text-white">
                                    {{ activity.manual_percentage }}%
                                </p>
                            </div>
                            <div
                                class="rounded-lg border border-white/10 bg-[#0c0f14]/50 p-4"
                            >
                                <p class="text-sm text-slate-500">Copy paste</p>
                                <p class="mt-1 text-2xl font-semibold text-[#eab308]">
                                    {{ activity.paste_percentage }}%
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bc-card p-6">
                    <h3 class="text-lg font-semibold text-white">
                        Review & defend
                    </h3>
                    <form class="mt-4 space-y-4" @submit.prevent="submit">
                        <div>
                            <label class="bc-label">Status</label>
                            <select v-model="form.status" class="bc-select mt-2">
                                <option value="pending">Pending</option>
                                <option value="in_progress">In progress</option>
                                <option value="submitted">Submitted</option>
                                <option value="reviewed">Reviewed</option>
                            </select>
                        </div>

                        <div>
                            <label class="bc-label"
                                >Defend round schedule</label
                            >
                            <input
                                v-model="form.defend_scheduled_at"
                                class="bc-input mt-2"
                                type="datetime-local"
                            />
                        </div>

                        <div>
                            <label class="bc-label">Score</label>
                            <input
                                v-model="form.score"
                                class="bc-input mt-2"
                                max="100"
                                min="0"
                                type="number"
                            />
                        </div>

                        <div>
                            <label class="bc-label">Defend outcome</label>
                            <select
                                v-model="form.defend_outcome"
                                class="bc-select mt-2"
                            >
                                <option value="">Not decided</option>
                                <option value="pass">Pass</option>
                                <option value="fail">Fail</option>
                                <option value="undecided">Undecided</option>
                            </select>
                        </div>

                        <div>
                            <label class="bc-label">Reviewer notes</label>
                            <textarea
                                v-model="form.reviewer_notes"
                                class="bc-input mt-2 min-h-[100px] resize-y"
                                rows="5"
                            />
                        </div>

                        <button class="bc-btn-primary w-full" type="submit">
                            Save review
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
