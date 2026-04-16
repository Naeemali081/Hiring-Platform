<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    assessment: {
        type: Object,
        required: true,
    },
    report: {
        type: Object,
        required: true,
    },
});

const formatCode = (code) => {
    if (!code) return [];
    return code.split('\n').map((line, i) => ({ num: i + 1, text: line }));
};
</script>

<template>
    <Head :title="`Submission Report — ${props.assessment.candidate_name}`" />

    <AuthenticatedLayout>
        <template #title>Submission Report — {{ props.assessment.candidate_name }}</template>

        <!-- Report Header -->
        <div class="bg-navy rounded-[14px] p-7 mb-6 text-white">
            <div class="text-[10px] uppercase tracking-[1.5px] text-white/45 mb-2">Submission Report</div>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="font-display text-[22px] font-extrabold">{{ props.assessment.candidate_name }}</h2>
                    <div class="text-[13px] text-white/50 mt-1">
                        {{ props.assessment.role_level }} Developer · {{ props.assessment.problem?.title }} · Submitted {{ props.report.time_taken_minutes || '?' }} min ago
                    </div>
                </div>
                <div class="flex gap-8 md:gap-10">
                    <div class="text-center">
                        <div class="font-display text-[28px] font-extrabold text-green">{{ props.report.manual_percentage }}%</div>
                        <div class="text-[10px] uppercase tracking-[0.8px] text-white/45">Manual Code</div>
                    </div>
                    <div class="text-center">
                        <div class="font-display text-[28px] font-extrabold text-red">{{ props.report.paste_percentage }}%</div>
                        <div class="text-[10px] uppercase tracking-[0.8px] text-white/45">Copy-Pasted</div>
                    </div>
                    <div class="text-center">
                        <div class="font-display text-[28px] font-extrabold">{{ props.report.time_taken_minutes || '?' }}<span class="text-lg">min</span></div>
                        <div class="text-[10px] uppercase tracking-[0.8px] text-white/45">Time Used</div>
                    </div>
                    <div class="text-center">
                        <div class="font-display text-[28px] font-extrabold text-amber">{{ props.assessment.submission?.tab_switches || 0 }}<span class="text-lg">×</span></div>
                        <div class="text-[10px] uppercase tracking-[0.8px] text-white/45">Tab Switches</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_380px] gap-6">
            <!-- LEFT: Submitted Code -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Submitted Code</div>
                    <span class="text-xs font-mono text-gray-3 bg-gray-1 px-2 py-1 rounded">{{ props.assessment.submission?.language || 'PHP' }} / {{ props.assessment.problem?.title || 'Laravel' }}</span>
                </div>
                <div class="card-body" style="padding: 0;">
                    <div class="bg-navy rounded-b-[14px] p-5 font-mono text-[13px] leading-[1.8] overflow-x-auto">
                        <div v-for="line in formatCode(props.assessment.submission?.code)" :key="line.num" class="code-line flex gap-4">
                            <span class="text-gray-4 select-none min-w-[24px] text-right">{{ line.num }}</span>
                            <span class="text-[#C8C8E8]">{{ line.text || ' ' }}</span>
                        </div>
                        <div v-if="!props.assessment.submission?.code" class="text-gray-3 text-center py-8">
                            No code submitted yet.
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Transparency Report -->
            <div class="flex flex-col gap-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Transparency Report</div>
                    </div>
                    <div class="card-body">
                        <!-- Progress Bar -->
                        <div class="h-3.5 rounded-full overflow-hidden flex mb-4">
                            <div class="bg-green" :style="{ width: props.report.manual_percentage + '%' }"></div>
                            <div class="bg-red" :style="{ width: props.report.paste_percentage + '%' }"></div>
                        </div>
                        <div class="flex justify-between text-xs mb-5">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-green"></span>
                                <span class="text-gray-4">Manual:</span>
                                <span class="font-bold">{{ props.report.manual_percentage }}%</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-red"></span>
                                <span class="text-gray-4">Pasted:</span>
                                <span class="font-bold">{{ props.report.paste_percentage }}%</span>
                            </div>
                        </div>

                        <!-- Stats List -->
                        <div class="space-y-3">
                            <div class="flex items-center gap-3 text-sm">
                                <span class="text-amber">📋</span>
                                <span class="text-gray-4">{{ props.assessment.submission?.paste_events || 0 }} paste events detected</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm">
                                <span class="text-gray-3">⌨️</span>
                                <span class="text-gray-4">{{ props.assessment.submission?.manual_chars || 0 }} keystrokes typed manually</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm">
                                <span class="text-purple">👁️</span>
                                <span class="text-gray-4">{{ props.assessment.submission?.tab_switches || 0 }} tab switches recorded</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Execution Result -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Execution Result</div>
                    </div>
                    <div class="card-body">
                        <div class="flex items-center gap-2 text-green mb-3">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="font-semibold">All Tests Passed</span>
                        </div>
                        <div class="text-xs text-gray-3">3/3 test cases</div>
                        <pre v-if="props.assessment.submission?.execution_result" class="mt-4 bg-gray-1 rounded-lg p-3 text-xs font-mono overflow-x-auto">
{{ JSON.stringify(props.assessment.submission.execution_result, null, 2) }}
                        </pre>
                    </div>
                </div>

                <!-- Reviewer Notes -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Reviewer Notes</div>
                    </div>
                    <div class="card-body">
                        <p v-if="props.assessment.submission?.reviewer_notes" class="text-sm text-white whitespace-pre-line">
                            {{ props.assessment.submission.reviewer_notes }}
                        </p>
                        <p v-else class="text-sm text-gray-3 italic">No notes added yet.</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
