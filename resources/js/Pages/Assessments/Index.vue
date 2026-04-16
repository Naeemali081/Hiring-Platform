<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    assessments: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
    problems: {
        type: Array,
        required: true,
    },
});

const currentStep = ref(1);
const totalSteps = 4;

const form = useForm({
    role_level: 'mid',
    problem_id: '',
    candidate_name: '',
    candidate_email: '',
    time_limit_minutes: 60,
    expires_in_days: 7,
});

const filter = useForm({
    status: props.filters.status ?? '',
});

const filteredProblems = computed(() => {
    return props.problems.filter(p => p.role_level === form.role_level);
});

const selectedProblem = computed(() => {
    return props.problems.find(p => p.id === form.problem_id);
});

const nextStep = () => {
    if (currentStep.value < totalSteps) currentStep.value++;
};

const prevStep = () => {
    if (currentStep.value > 1) currentStep.value--;
};

const selectRole = (role) => {
    form.role_level = role;
    form.problem_id = '';
    nextStep();
};

const selectProblem = (problemId) => {
    form.problem_id = problemId;
    nextStep();
};

const submit = () => {
    form.post(route('assessments.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('candidate_name', 'candidate_email', 'problem_id');
            currentStep.value = 1;
        },
    });
};

const applyFilters = () => {
    router.get(route('assessments.index'), filter.data(), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const formatTimeAgo = (date) => {
    if (!date) return '-';
    const now = new Date();
    const then = new Date(date);
    const diff = Math.floor((now - then) / 1000 / 60);
    if (diff < 60) return `${diff}m ago`;
    if (diff < 1440) return `${Math.floor(diff / 60)}h ago`;
    return `${Math.floor(diff / 1440)}d ago`;
};
</script>

<template>
    <Head title="Assessments" />

    <AuthenticatedLayout>
        <template #title>New Assessment</template>

        <!-- Stepper -->
        <div class="bg-white rounded-xl p-1.5 border border-gray-2 flex gap-0 mb-8">
            <button
                v-for="step in totalSteps"
                :key="step"
                @click="currentStep = step"
                :class="[
                    'flex-1 py-2.5 px-4 rounded-lg text-xs font-semibold text-center transition-all',
                    currentStep === step ? 'bg-purple text-white' : 'text-gray-3 hover:text-gray-4',
                    step < currentStep ? 'text-green' : ''
                ]"
            >
                <span v-if="step === 1">1. Role Level</span>
                <span v-else-if="step === 2">2. Select Problem</span>
                <span v-else-if="step === 3">3. Candidate Info</span>
                <span v-else>4. Review & Send</span>
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_380px] gap-6">
            <!-- LEFT: Creation Form -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <span v-if="currentStep === 1">Select Role Level</span>
                        <span v-else-if="currentStep === 2">Select Problem</span>
                        <span v-else-if="currentStep === 3">Candidate Details</span>
                        <span v-else>Review & Send</span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Step 1: Role Level -->
                    <div v-if="currentStep === 1" class="grid grid-cols-3 gap-3">
                        <button
                            @click="selectRole('junior')"
                            :class="['border-2 border-gray-2 rounded-xl p-5 text-center transition-all hover:border-purple', form.role_level === 'junior' ? 'border-purple bg-purple/[0.04]' : '']"
                        >
                            <div class="text-2xl mb-2">🌱</div>
                            <div class="font-display text-sm font-bold">Junior</div>
                            <div class="text-[11.5px] text-gray-3 mt-1">0-2 years experience</div>
                            <div class="inline-block mt-2 text-[10px] font-semibold text-green bg-green-light px-2 py-0.5 rounded">Easy tasks</div>
                        </button>
                        <button
                            @click="selectRole('mid')"
                            :class="['border-2 border-gray-2 rounded-xl p-5 text-center transition-all hover:border-purple', form.role_level === 'mid' ? 'border-purple bg-purple/[0.04]' : '']"
                        >
                            <div class="text-2xl mb-2">⚡</div>
                            <div class="font-display text-sm font-bold">Mid-level</div>
                            <div class="text-[11.5px] text-gray-3 mt-1">2-5 years experience</div>
                            <div class="inline-block mt-2 text-[10px] font-semibold text-purple bg-purple/10 px-2 py-0.5 rounded">Medium tasks</div>
                        </button>
                        <button
                            @click="selectRole('senior')"
                            :class="['border-2 border-gray-2 rounded-xl p-5 text-center transition-all hover:border-purple', form.role_level === 'senior' ? 'border-purple bg-purple/[0.04]' : '']"
                        >
                            <div class="text-2xl mb-2">🏆</div>
                            <div class="font-display text-sm font-bold">Senior</div>
                            <div class="text-[11.5px] text-gray-3 mt-1">5+ years experience</div>
                            <div class="inline-block mt-2 text-[10px] font-semibold text-amber bg-amber-light px-2 py-0.5 rounded">Complex tasks</div>
                        </button>
                    </div>

                    <!-- Step 2: Select Problem -->
                    <div v-if="currentStep === 2">
                        <p v-if="filteredProblems.length === 0" class="text-gray-3 text-center py-8">
                            No problems available for {{ form.role_level }} level. <br>
                            <Link :href="route('problems.index')" class="text-purple hover:underline">Create a problem first</Link>
                        </p>
                        <div v-else class="flex flex-col gap-2.5 max-h-[360px] overflow-y-auto">
                            <button
                                v-for="problem in filteredProblems"
                                :key="problem.id"
                                @click="selectProblem(problem.id)"
                                :class="['border-2 rounded-xl p-4 flex items-center justify-between transition-all hover:border-purple text-left', form.problem_id === problem.id ? 'border-purple bg-purple/[0.04]' : 'border-gray-2']"
                            >
                                <div>
                                    <div class="text-[13.5px] font-semibold mb-1">{{ problem.title }}</div>
                                    <div class="flex gap-1.5 flex-wrap">
                                        <span v-for="tag in (problem.tags || [])" :key="tag" class="tag">{{ tag }}</span>
                                    </div>
                                </div>
                                <div class="text-xs font-bold text-purple bg-purple/10 px-2.5 py-1 rounded-full whitespace-nowrap flex-shrink-0">
                                    {{ problem.suggested_time_minutes }} min
                                </div>
                            </button>
                        </div>
                        <button @click="prevStep" class="btn btn-ghost mt-4">← Back</button>
                    </div>

                    <!-- Step 3: Candidate Info -->
                    <form v-if="currentStep === 3" @submit.prevent="nextStep">
                        <div class="space-y-4">
                            <div>
                                <label class="form-label">Candidate Name</label>
                                <input v-model="form.candidate_name" type="text" class="form-input" placeholder="Enter candidate name" required />
                            </div>
                            <div>
                                <label class="form-label">Candidate Email</label>
                                <input v-model="form.candidate_email" type="email" class="form-input" placeholder="candidate@example.com" required />
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label">Time Limit (minutes)</label>
                                    <input v-model="form.time_limit_minutes" type="number" min="15" max="240" class="form-input" />
                                </div>
                                <div>
                                    <label class="form-label">Expires In (days)</label>
                                    <input v-model="form.expires_in_days" type="number" min="1" max="30" class="form-input" />
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-3 mt-6">
                            <button type="button" @click="prevStep" class="btn btn-ghost">← Back</button>
                            <button type="submit" class="btn btn-primary flex-1">Next: Review →</button>
                        </div>
                    </form>

                    <!-- Step 4: Review & Send -->
                    <div v-if="currentStep === 4">
                        <div class="bg-gray-1 rounded-lg p-4 space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-4">Role Level:</span>
                                <span class="font-semibold capitalize">{{ form.role_level }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-4">Problem:</span>
                                <span class="font-semibold">{{ selectedProblem?.title }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-4">Candidate:</span>
                                <span class="font-semibold">{{ form.candidate_name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-4">Email:</span>
                                <span class="font-semibold">{{ form.candidate_email }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-4">Time Limit:</span>
                                <span class="font-semibold">{{ form.time_limit_minutes }} minutes</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-4">Expires:</span>
                                <span class="font-semibold">{{ form.expires_in_days }} days</span>
                            </div>
                        </div>

                        <p v-if="$page.props.auth.user.role === 'interviewer'" class="mt-4 text-xs text-amber bg-amber-light p-3 rounded-lg">
                            ⚠️ Interviewers can only review and defend assessments. Only company admins can create assessments.
                        </p>

                        <div class="flex gap-3 mt-6">
                            <button @click="prevStep" class="btn btn-ghost">← Back</button>
                            <button
                                @click="submit"
                                :disabled="form.processing || $page.props.auth.user.role === 'interviewer'"
                                class="btn btn-primary flex-1 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {{ form.processing ? 'Sending...' : 'Send Assessment →' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Assessment List -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Assessment List</div>
                    <span class="sidebar-nav-badge">{{ assessments.length }}</span>
                </div>
                <div class="card-body">
                    <select v-model="filter.status" @change="applyFilters" class="form-select mb-4">
                        <option value="">All statuses</option>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="submitted">Submitted</option>
                        <option value="reviewed">Reviewed</option>
                    </select>

                    <p v-if="assessments.length === 0" class="text-center py-8 text-gray-3 text-sm">
                        No assessments created yet.
                    </p>
                    <div v-else class="flex flex-col gap-3 max-h-[500px] overflow-y-auto">
                        <Link
                            v-for="assessment in assessments.slice(0, 10)"
                            :key="assessment.id"
                            :href="route('assessments.show', assessment.id)"
                            class="border border-gray-2 rounded-lg p-3 hover:border-purple transition-all group"
                        >
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <div class="text-[13px] font-semibold text-white truncate">{{ assessment.candidate_name }}</div>
                                    <div class="text-xs text-gray-3 truncate">{{ assessment.problem?.title }}</div>
                                    <div class="flex items-center gap-2 mt-1.5">
                                        <span :class="['role-tag', assessment.role_level]">{{ assessment.role_level }}</span>
                                        <span :class="['status-badge', assessment.status === 'in_progress' ? 'progress' : assessment.status]">
                                            {{ assessment.status.replace('_', ' ') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-3 flex-shrink-0">{{ formatTimeAgo(assessment.created_at) }}</div>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
