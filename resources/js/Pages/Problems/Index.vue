<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import axios from 'axios';
import { marked } from 'marked';
import DOMPurify from 'dompurify';
import hljs from 'highlight.js';
import 'highlight.js/styles/github-dark.css';

marked.setOptions({
    highlight: function(code, lang) {
        if (lang && hljs.getLanguage(lang)) {
            return hljs.highlight(code, { language: lang }).value;
        }
        return hljs.highlightAuto(code).value;
    }
});

const props = defineProps({
    filters: {
        type: Object,
        required: true,
    },
    libraryProblems: {
        type: Array,
        required: true,
    },
    problems: {
        type: Array,
        required: true,
    },
});

const showCreateModal = ref(false);
const activeTab = ref(props.filters.role_level || 'all');
const isPreviewMode = ref(false);

const form = useForm({
    title: '',
    description: '',
    difficulty: 'medium',
    role_level: 'mid',
    suggested_time_minutes: 60,
    tags: '',
    is_public: false,
});

const renderedDescription = computed(() => {
    return DOMPurify.sanitize(marked.parse(form.description || ''));
});

const submit = () => {
    form.post(route('problems.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('title', 'description', 'tags');
            showCreateModal.value = false;
        },
    });
};

const isSuggestingTime = ref(false);
const suggestTime = async () => {
    if (!form.title || !form.description) {
        alert('Please fill in the title and description first so the AI has context!');
        return;
    }
    
    isSuggestingTime.value = true;
    try {
        const response = await axios.post(route('problems.suggestTime'), {
            title: form.title,
            description: form.description,
            difficulty: form.difficulty,
            role_level: form.role_level
        });
        
        if (response.data.suggested_minutes) {
            form.suggested_time_minutes = response.data.suggested_minutes;
        }
    } catch (error) {
        console.error('AI Suggestion Error:', error);
        alert('Could not reach AI service. Please set time manually.');
    } finally {
        isSuggestingTime.value = false;
    }
};

const filter = useForm({
    role_level: props.filters.role_level ?? '',
    search: props.filters.search ?? '',
});

const applyFilters = () => {
    router.get(route('problems.index'), filter.data(), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const setTab = (tab) => {
    activeTab.value = tab;
    filter.role_level = tab === 'all' ? '' : tab;
    applyFilters();
};

const allProblems = computed(() => {
    return [...props.problems, ...props.libraryProblems];
});

const filteredProblems = computed(() => {
    let result = allProblems.value;
    if (activeTab.value !== 'all') {
        result = result.filter(p => p.role_level === activeTab.value);
    }
    if (filter.search) {
        result = result.filter(p => p.title.toLowerCase().includes(filter.search.toLowerCase()));
    }
    return result;
});

const difficultyColor = (difficulty) => {
    const colors = {
        easy: 'text-green',
        medium: 'text-amber',
        hard: 'text-red',
    };
    return colors[difficulty] || 'text-gray-4';
};
</script>

<template>
    <Head title="Problem Bank" />

    <AuthenticatedLayout>
        <template #title>Problem Bank</template>

        <!-- Filters & Add Button -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div class="flex flex-wrap items-center gap-2">
                <button
                    @click="setTab('all')"
                    :class="['px-4 py-2 rounded-lg text-sm font-semibold transition-all', activeTab === 'all' ? 'bg-purple text-white' : 'bg-white border border-gray-2 text-gray-4 hover:border-purple hover:text-purple']"
                >
                    All
                </button>
                <button
                    @click="setTab('junior')"
                    :class="['px-4 py-2 rounded-lg text-sm font-semibold transition-all', activeTab === 'junior' ? 'bg-purple text-white' : 'bg-white border border-gray-2 text-gray-4 hover:border-purple hover:text-purple']"
                >
                    Junior
                </button>
                <button
                    @click="setTab('mid')"
                    :class="['px-4 py-2 rounded-lg text-sm font-semibold transition-all', activeTab === 'mid' ? 'bg-purple text-white' : 'bg-white border border-gray-2 text-gray-4 hover:border-purple hover:text-purple']"
                >
                    Mid-level
                </button>
                <button
                    @click="setTab('senior')"
                    :class="['px-4 py-2 rounded-lg text-sm font-semibold transition-all', activeTab === 'senior' ? 'bg-purple text-white' : 'bg-white border border-gray-2 text-gray-4 hover:border-purple hover:text-purple']"
                >
                    Senior
                </button>
            </div>
            <button
                @click="showCreateModal = true"
                class="btn btn-primary"
                :disabled="$page.props.auth.user.role === 'interviewer'"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Custom Problem
            </button>
        </div>

        <!-- Problems Table -->
        <div class="card">
            <div class="overflow-x-auto">
                <table class="bc-table">
                    <thead>
                        <tr>
                            <th>Problem Title</th>
                            <th>Role</th>
                            <th>Difficulty</th>
                            <th>Tags</th>
                            <th>AI Time</th>
                            <th>Source</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="problem in filteredProblems" :key="problem.id">
                            <td>
                                <strong class="text-white">{{ problem.title }}</strong>
                            </td>
                            <td>
                                <span :class="['role-tag', problem.role_level]">{{ problem.role_level }}</span>
                            </td>
                            <td>
                                <span :class="['font-semibold capitalize', difficultyColor(problem.difficulty)]">
                                    {{ problem.difficulty }}
                                </span>
                            </td>
                            <td>
                                <div class="flex gap-1 flex-wrap">
                                    <span v-for="tag in (problem.tags || []).slice(0, 3)" :key="tag" class="tag">{{ tag }}</span>
                                </div>
                            </td>
                            <td class="text-gray-4">{{ problem.suggested_time_minutes }} min</td>
                            <td>
                                <span
                                    :class="[
                                        'text-xs font-semibold px-2 py-1 rounded-full',
                                        problem.is_public || !problem.company_id ? 'text-green bg-green-light' : 'text-purple bg-purple/10'
                                    ]"
                                >
                                    {{ problem.is_public || !problem.company_id ? 'Platform' : 'Custom' }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="filteredProblems.length === 0">
                            <td colspan="6" class="text-center py-12 text-gray-3">
                                <div class="text-4xl mb-3">📁</div>
                                <div class="text-sm">No problems found for this filter.</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create Problem Modal -->
        <Modal :show="showCreateModal" @close="showCreateModal = false">
            <div class="p-6">
                <h3 class="card-title mb-4">Create Custom Problem</h3>
                <p v-if="$page.props.auth.user.role === 'interviewer'" class="mb-4 text-xs text-amber bg-amber-light p-2 rounded">
                    Interviewers can review problems; only company admins can create them.
                </p>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="form-label">Title</label>
                        <input v-model="form.title" type="text" class="form-input" placeholder="Problem title" required />
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="form-label mb-0">Description</label>
                            <div class="flex bg-surface-elevated border border-surface-border rounded-lg p-1">
                                <button type="button" @click="isPreviewMode = false" :class="['px-4 py-1 text-xs font-semibold rounded-md transition-colors', !isPreviewMode ? 'bg-surface text-white border border-surface-border shadow-sm' : 'text-gray-4 hover:text-white']">Write</button>
                                <button type="button" @click="isPreviewMode = true" :class="['px-4 py-1 text-xs font-semibold rounded-md transition-colors', isPreviewMode ? 'bg-surface text-white border border-surface-border shadow-sm' : 'text-gray-4 hover:text-white']">Preview</button>
                            </div>
                        </div>

                        <div v-show="!isPreviewMode">
                            <textarea v-model="form.description" class="form-textarea font-mono text-[13px] leading-relaxed" rows="8" placeholder="Use Markdown to format your question. Example:&#10;&#10;Write a function `sum(a, b)` that returns...&#10;&#10;```javascript&#10;function sum(a, b) {&#10;}&#10;```" required></textarea>
                            <div class="text-[11px] text-gray-4 mt-2 flex justify-between px-1">
                                <span>Markdown formatting supported.</span>
                                <a href="https://www.markdownguide.org/cheat-sheet/" target="_blank" class="hover:text-purple transition-colors">Help</a>
                            </div>
                        </div>
                        
                        <div v-show="isPreviewMode" class="markdown-body p-5 bg-[#0D0D1E] border border-surface-border rounded-xl min-h-[220px] max-h-[400px] overflow-y-auto">
                            <div v-if="form.description" v-html="renderedDescription"></div>
                            <div v-else class="text-gray-4 italic text-sm text-center mt-12">Nothing to preview.</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Difficulty</label>
                            <select v-model="form.difficulty" class="form-select">
                                <option value="easy">Easy</option>
                                <option value="medium">Medium</option>
                                <option value="hard">Hard</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Role Level</label>
                            <select v-model="form.role_level" class="form-select">
                                <option value="junior">Junior</option>
                                <option value="mid">Mid</option>
                                <option value="senior">Senior</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="form-label mb-0">Time Limit (minutes)</label>
                                <button type="button" @click="suggestTime" :disabled="isSuggestingTime" class="text-[11px] font-bold text-purple-glow hover:text-purple transition-all flex items-center gap-1 active:scale-95 bg-purple/10 px-2 py-0.5 rounded-full border border-purple/30 shadow-[0_0_8px_rgba(124,110,250,0.2)]">
                                    <span v-if="isSuggestingTime" class="animate-spin inline-block">⏳</span>
                                    <span v-else>✨</span>
                                    AI Suggest
                                </button>
                            </div>
                            <input v-model="form.suggested_time_minutes" type="number" min="15" max="240" :class="['form-input transition-all duration-500', isSuggestingTime ? 'animate-pulse bg-purple/10 border-purple/30 text-purple-glow' : '']" />
                        </div>
                        <div>
                            <label class="form-label">Tags (comma separated)</label>
                            <input v-model="form.tags" type="text" class="form-input" placeholder="Laravel, API, etc" />
                        </div>
                    </div>
                    <label class="flex items-center gap-2 text-sm">
                        <input v-model="form.is_public" type="checkbox" class="rounded border-gray-2" />
                        <span class="text-gray-4">Include in shared library</span>
                    </label>
                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="showCreateModal = false" class="btn btn-ghost">Cancel</button>
                        <button
                            type="submit"
                            :disabled="form.processing || $page.props.auth.user.role === 'interviewer'"
                            class="btn btn-primary flex-1"
                        >
                            {{ form.processing ? 'Saving...' : 'Save Problem' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
