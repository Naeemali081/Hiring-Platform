<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    company: {
        type: Object,
        required: true,
    },
    stats: {
        type: Object,
        required: true,
    },
    recentAssessments: {
        type: Array,
        required: true,
    },
    roleBreakdown: {
        type: Object,
        required: true,
    },
});

const formatTimeAgo = (date) => {
    if (!date) return '-';
    const now = new Date();
    const then = new Date(date);
    const diff = Math.floor((now - then) / 1000 / 60);
    if (diff < 60) return `${diff}m ago`;
    if (diff < 1440) return `${Math.floor(diff / 60)}h ago`;
    return `${Math.floor(diff / 1440)}d ago`;
};

const getRoleMax = () => {
    return Math.max(props.roleBreakdown?.junior || 1, props.roleBreakdown?.mid || 1, props.roleBreakdown?.senior || 1);
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #title>Dashboard</template>

        <!-- Plan Bar -->
        <div class="plan-bar">
            <div class="flex items-center gap-3">
                <div class="text-xl">⚡</div>
                <div>
                    <div class="plan-name capitalize">{{ company.plan }} Plan</div>
                    <div class="plan-detail">Renews {{ company.plan_expires_at || 'July 14, 2026' }}</div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="usage-track">
                    <div class="usage-fill" :style="{ width: (company.total_assessments / 100 * 100) + '%' }"></div>
                </div>
                <div class="usage-text">{{ company.total_assessments }} / 100 assessments used</div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-7">
            <div class="stat-card purple">
                <div class="stat-label">Total Assessments</div>
                <div class="stat-value">{{ stats.assessments || 0 }}</div>
                <div class="stat-badge up">↑ 12% this month</div>
            </div>
            <div class="stat-card green">
                <div class="stat-label">Submitted</div>
                <div class="stat-value">{{ stats.submitted || 0 }}</div>
                <div class="stat-badge up">↑ 6 this week</div>
            </div>
            <div class="stat-card amber">
                <div class="stat-label">Pending</div>
                <div class="stat-value">{{ stats.pending || 0 }}</div>
                <div class="text-xs text-gray-3 mt-1">3 expiring soon</div>
            </div>
            <div class="stat-card red">
                <div class="stat-label">Avg Paste Ratio</div>
                <div class="stat-value">{{ stats.average_paste_rate || 0 }}%</div>
                <div class="stat-badge down">↑ 3% vs last month</div>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-[2fr_1fr] gap-5">
            <!-- Recent Assessments Table -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Recent Assessments</div>
                    <Link :href="route('assessments.index')" class="btn btn-ghost text-xs px-3 py-1.5">
                        View all
                    </Link>
                </div>
                <div class="overflow-x-auto">
                    <table class="bc-table">
                        <thead>
                            <tr>
                                <th>Candidate</th>
                                <th>Role</th>
                                <th>Problem</th>
                                <th>Status</th>
                                <th>Sent</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="assessment in recentAssessments.slice(0, 5)" :key="assessment.id"
                                @click="$inertia.visit(route('assessments.show', assessment.id))">
                                <td>
                                    <strong class="text-white">{{ assessment.candidate_name }}</strong>
                                    <div class="text-[11.5px] text-gray-3">{{ assessment.candidate_email }}</div>
                                </td>
                                <td>
                                    <span :class="['role-tag', assessment.role_level]">{{ assessment.role_level }}</span>
                                </td>
                                <td>{{ assessment.problem?.title || 'Unknown' }}</td>
                                <td>
                                    <span :class="['status-badge', assessment.status === 'in_progress' ? 'progress' : assessment.status]">
                                        {{ assessment.status === 'in_progress' ? 'In Progress' : assessment.status }}
                                    </span>
                                </td>
                                <td class="text-gray-3 text-xs">{{ formatTimeAgo(assessment.created_at) }}</td>
                            </tr>
                            <tr v-if="recentAssessments.length === 0">
                                <td colspan="5" class="text-center py-8 text-gray-3">
                                    No assessments yet. Create one to get started.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right Column: Role Distribution & Activity -->
            <div class="flex flex-col gap-5">
                <!-- Role Distribution -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Role Distribution</div>
                    </div>
                    <div class="card-body">
                        <div class="chart-bar-wrap">
                            <div class="chart-bar-item">
                                <div class="chart-bar-label">
                                    <span>Senior</span>
                                    <span>{{ roleBreakdown.senior || 0 }}</span>
                                </div>
                                <div class="chart-bar-track">
                                    <div class="chart-bar-fill bg-amber" :style="{ width: ((roleBreakdown.senior || 0) / getRoleMax() * 100) + '%' }"></div>
                                </div>
                            </div>
                            <div class="chart-bar-item">
                                <div class="chart-bar-label">
                                    <span>Mid-level</span>
                                    <span>{{ roleBreakdown.mid || 0 }}</span>
                                </div>
                                <div class="chart-bar-track">
                                    <div class="chart-bar-fill bg-purple" :style="{ width: ((roleBreakdown.mid || 0) / getRoleMax() * 100) + '%' }"></div>
                                </div>
                            </div>
                            <div class="chart-bar-item">
                                <div class="chart-bar-label">
                                    <span>Junior</span>
                                    <span>{{ roleBreakdown.junior || 0 }}</span>
                                </div>
                                <div class="chart-bar-track">
                                    <div class="chart-bar-fill bg-green" :style="{ width: ((roleBreakdown.junior || 0) / getRoleMax() * 100) + '%' }"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Feed -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Activity</div>
                    </div>
                    <div class="card-body" style="padding: 12px 20px;">
                        <div class="flex flex-col">
                            <div v-for="(assessment, index) in recentAssessments.slice(0, 3)" :key="index" class="feed-item">
                                <div class="feed-dot" :class="{
                                    'green': assessment.status === 'submitted',
                                    'purple': assessment.status === 'pending',
                                    'amber': assessment.status === 'in_progress',
                                    'red': assessment.status === 'reviewed'
                                }">
                                    {{ assessment.status === 'submitted' ? '✅' : assessment.status === 'pending' ? '📨' : '🗓️' }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="feed-title truncate">
                                        {{ assessment.candidate_name }} {{ assessment.status === 'submitted' ? 'submitted' : assessment.status === 'pending' ? 'invited' : 'scheduled' }}
                                    </div>
                                    <div class="feed-sub truncate">
                                        {{ assessment.problem?.title || 'Unknown' }} — {{ assessment.role_level }}
                                    </div>
                                </div>
                                <div class="feed-time">{{ formatTimeAgo(assessment.created_at) }}</div>
                            </div>
                            <div v-if="recentAssessments.length === 0" class="text-center py-6 text-gray-3 text-sm">
                                No recent activity
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
