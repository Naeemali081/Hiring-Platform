<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    reports: {
        type: Array,
        required: true,
    },
    summary: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head title="Reports" />

    <AuthenticatedLayout>
        <template #title>Reports & Analytics</template>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-6">
            <div class="stat-card purple">
                <div class="stat-label">Average Score</div>
                <div class="stat-value">{{ props.summary.average_score || 0 }}</div>
            </div>
            <div class="stat-card amber">
                <div class="stat-label">Avg Tab Switches</div>
                <div class="stat-value">{{ props.summary.average_tab_switches || 0 }}</div>
            </div>
            <div class="stat-card green">
                <div class="stat-label">Defend Pass Count</div>
                <div class="stat-value">{{ props.summary.pass_count || 0 }}</div>
                <div class="stat-badge up">Passed</div>
            </div>
        </div>

        <!-- Reports Table -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">Submission Reports</div>
                <span class="sidebar-nav-badge">{{ props.reports.length }}</span>
            </div>
            <div class="overflow-x-auto">
                <div v-if="props.reports.length === 0" class="text-center py-12 text-gray-3">
                    <div class="text-4xl mb-3">📊</div>
                    <div class="text-sm">No reports available yet.</div>
                </div>
                <table v-else class="bc-table">
                    <thead>
                        <tr>
                            <th>Candidate</th>
                            <th>Problem</th>
                            <th>Score</th>
                            <th>Paste Rate</th>
                            <th>Tab Switches</th>
                            <th>Defend</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="report in props.reports" :key="report.id">
                            <td>
                                <strong class="text-white">{{ report.candidate_name }}</strong>
                                <div class="text-[11.5px] text-gray-3">{{ report.candidate_email }}</div>
                            </td>
                            <td class="text-white">{{ report.problem_title }}</td>
                            <td class="font-semibold">{{ report.score ?? 'N/A' }}</td>
                            <td>
                                <span :class="['font-semibold', report.paste_rate > 50 ? 'text-red' : 'text-green']">
                                    {{ report.paste_rate }}%
                                </span>
                            </td>
                            <td class="text-gray-4">{{ report.tab_switches }}</td>
                            <td>
                                <span v-if="report.defend_outcome" :class="['status-badge', report.defend_outcome === 'pass' ? 'submitted' : report.defend_outcome === 'fail' ? 'pending' : 'reviewed']">
                                    {{ report.defend_outcome }}
                                </span>
                                <span v-else class="text-gray-3 text-xs">Pending</span>
                            </td>
                            <td>
                                <Link :href="route('reports.show', report.id)" class="text-purple font-semibold hover:underline text-sm">
                                    View →
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
