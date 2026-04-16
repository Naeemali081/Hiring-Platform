<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth.user);
const company = computed(() => page.props.company || user.value?.company);

const menuItems = [
    { name: 'Dashboard', route: 'dashboard', icon: 'dashboard', section: 'Main' },
    { name: 'Assessments', route: 'assessments.index', icon: 'assessments', section: 'Main', badge: 3 },
    { name: 'New Assessment', route: 'assessments.index', icon: 'plus', section: 'Main' },
    { name: 'Problem Bank', route: 'problems.index', icon: 'problems', section: 'Main' },
    { name: 'Reports', route: 'reports.index', icon: 'reports', section: 'Reports' },
    { name: 'Candidate View', route: 'problems.preview', icon: 'eye', section: 'Preview', params: { problem: 1 } },
];

const getInitials = (name) => {
    return name?.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2) || 'U';
};

const isActive = (item) => {
    const routeName = item.route;
    // For routes with required params, check if URL contains the route pattern
    if (item.params) {
        try {
            return page.url.startsWith(route(routeName, item.params));
        } catch (e) {
            return false;
        }
    }
    // For routes without params, check by generating the route URL
    try {
        return page.url.startsWith(route(routeName));
    } catch (e) {
        // If route generation fails, check if route name is in URL
        return page.url.includes(routeName.replace('.', '/'));
    }
};
</script>

<template>
    <div class="min-h-screen">
        <!-- SIDEBAR -->
        <aside class="sidebar">
            <!-- Logo -->
            <div class="sidebar-logo">
                <Link :href="route('dashboard')" class="flex items-center gap-3 w-full overflow-hidden group">
                    <img src="/images/logo.png" alt="BrainCode 360" class="h-9 w-9 object-contain flex-shrink-0 transition-transform duration-300 group-hover:scale-110 drop-shadow-[0_0_10px_rgba(124,110,250,0.4)]" />
                    <div class="flex flex-col min-w-0">
                        <div class="sidebar-logo-text text-[18px] truncate">
                            Brain<span>Code</span>
                        </div>
                        <div class="sidebar-logo-sub truncate">Hiring Platform</div>
                    </div>
                </Link>
            </div>

            <!-- Navigation -->
            <nav class="sidebar-nav">
                <template v-for="(item, index) in menuItems" :key="item.route">
                    <!-- Section Header -->
                    <div v-if="index === 0 || menuItems[index - 1].section !== item.section" class="sidebar-nav-section">
                        {{ item.section }}
                    </div>

                    <!-- Nav Item -->
                    <Link
                        :href="item.params ? route(item.route, item.params) : route(item.route)"
                        :class="['sidebar-nav-item', { 'active': isActive(item) }]"
                    >
                        <!-- Icons -->
                        <svg v-if="item.icon === 'dashboard'" class="sidebar-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7" rx="1"/>
                            <rect x="14" y="3" width="7" height="7" rx="1"/>
                            <rect x="3" y="14" width="7" height="7" rx="1"/>
                            <rect x="14" y="14" width="7" height="7" rx="1"/>
                        </svg>
                        <svg v-else-if="item.icon === 'assessments'" class="sidebar-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <svg v-else-if="item.icon === 'problems'" class="sidebar-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                        </svg>
                        <svg v-else-if="item.icon === 'reports'" class="sidebar-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <svg v-else-if="item.icon === 'plus'" class="sidebar-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M12 4v16m8-8H4"/>
                        </svg>
                        <svg v-else-if="item.icon === 'eye'" class="sidebar-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>

                        {{ item.name }}

                        <!-- Badge -->
                        <span v-if="item.badge" class="sidebar-nav-badge">{{ item.badge }}</span>
                    </Link>
                </template>
            </nav>

            <!-- User Footer -->
            <div class="sidebar-footer">
                <Link :href="route('profile.edit')" class="flex items-center gap-2.5">
                    <div class="user-avatar">{{ getInitials(user.name) }}</div>
                    <div>
                        <div class="text-xs font-semibold text-white">{{ user.name }}</div>
                        <div class="user-plan-badge">{{ company?.plan || 'Free' }} Plan</div>
                    </div>
                </Link>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <div class="main-content">
            <!-- Topbar -->
            <header class="topbar">
                <h1 class="page-title">
                    <slot name="title">Dashboard</slot>
                </h1>
                <div class="flex items-center gap-3">
                    <Link :href="route('assessments.index')" class="btn btn-ghost text-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Refresh
                    </Link>
                    <Link :href="route('assessments.index')" class="btn btn-primary">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        New Assessment
                    </Link>
                </div>
            </header>

            <!-- Flash Messages -->
            <div v-if="$page.props.flash?.success" class="mx-8 mt-4">
                <div class="bg-green-light text-green px-4 py-3 rounded-lg text-sm font-medium">
                    {{ $page.props.flash.success }}
                </div>
            </div>
            <div v-if="$page.props.flash?.error" class="mx-8 mt-4">
                <div class="bg-red-light text-red px-4 py-3 rounded-lg text-sm font-medium">
                    {{ $page.props.flash.error }}
                </div>
            </div>

            <!-- Page Content -->
            <main class="content-area">
                <Transition name="page" mode="out-in">
                    <div :key="$page.url" class="h-full">
                        <slot />
                    </div>
                </Transition>
            </main>
        </div>
    </div>
</template>
