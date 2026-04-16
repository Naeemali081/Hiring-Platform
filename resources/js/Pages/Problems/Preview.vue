<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    problem: {
        type: Object,
        required: true,
    },
});

const code = ref(`namespace App\\Http\\Controllers\\Api;

use App\\Models\\User;
use Illuminate\\Http\\Request;
use Illuminate\\Support\\Facades\\Hash;

class AuthController extends Controller
{
  public function login(Request $request)
  {
    $request->validate([
      'email' => 'required|email',
      'password' => 'required|min:8',
    ]);
    // Find user and verify password
    $user = User::where('email', $request->email)->first();`);

const timeLeft = ref(42 * 60 + 17); // 42:17 in seconds
const timerWarning = ref(false);
const timerDanger = ref(false);
let timerInterval = null;

const formatTime = (seconds) => {
    const m = Math.floor(seconds / 60).toString().padStart(2, '0');
    const s = (seconds % 60).toString().padStart(2, '0');
    return `${m}:${s}`;
};

const keystrokes = ref(412);
const pastes = ref(2);
const lines = computed(() => code.value.split('\n').length);

const language = ref('PHP');

onMounted(() => {
    timerInterval = setInterval(() => {
        if (timeLeft.value > 0) {
            timeLeft.value--;
            timerWarning.value = timeLeft.value < 600; // < 10 min
            timerDanger.value = timeLeft.value < 300; // < 5 min
        }
    }, 1000);
});

onUnmounted(() => {
    clearInterval(timerInterval);
});

const submitCode = () => {
    alert('Code submitted successfully! ✅');
};
</script>

<template>
    <Head title="Candidate Preview" />

    <div class="min-h-screen bg-[#0D0D1E] flex flex-col">
        <!-- Top Bar -->
        <div class="bg-[#111127] h-[52px] flex items-center justify-between px-6 border-b border-white/[0.06] flex-shrink-0">
            <div class="font-display text-[15px] font-extrabold text-white">
                Brain<span class="text-purple-2">Code</span> 360
            </div>
            <div class="flex items-center gap-4">
                <div class="text-xs text-white/40">
                    {{ problem.role_level }} · {{ problem.title }}
                </div>
                <div
                    :class="[
                        'font-mono text-[22px] font-bold text-white px-4 py-1.5 rounded-lg border tracking-[2px]',
                        timerDanger
                            ? 'bg-red/10 border-red/40 text-red animate-pulse'
                            : timerWarning
                                ? 'bg-amber/10 border-amber/40 text-amber'
                                : 'bg-purple/15 border-purple/30'
                    ]"
                >
                    {{ formatTime(timeLeft) }}
                </div>
            </div>
            <button
                @click="submitCode"
                class="bg-green text-[#0A1A14] px-6 py-2.5 rounded-lg font-display text-[13px] font-extrabold tracking-[0.3px] hover:shadow-[0_4px_16px_rgba(34,211,160,0.3)] hover:-translate-y-px transition-all"
            >
                Submit Code
            </button>
        </div>

        <!-- Main Content -->
        <div class="flex flex-1 overflow-hidden">
            <!-- Problem Panel -->
            <div class="w-[380px] bg-[#111127] border-r border-white/[0.06] overflow-y-auto p-6 flex-shrink-0">
                <div class="text-[10px] text-white/30 uppercase tracking-[1.5px] mb-3.5">Problem</div>
                <h1 class="font-display text-base font-bold text-white mb-1.5">{{ problem.title }}</h1>
                <div class="flex gap-2 mb-4">
                    <span :class="['role-tag', problem.role_level]">{{ problem.role_level }}</span>
                    <span class="time-chip">⏱ {{ problem.suggested_time_minutes }} min</span>
                </div>
                <div class="text-[13.5px] text-white/65 leading-[1.75]">
                    <div class="mb-3" v-html="problem.description?.replace(/\n/g, '<br>') || 'No description provided.'"></div>
                    <p><strong class="text-white/80">Requirements:</strong></p>
                    <p>1. Implement the solution according to the problem statement.</p>
                    <p>2. Follow best practices for {{ problem.role_level }} level developers.</p>
                    <p>3. Ensure code is clean and well-documented.</p>
                    <p><strong class="text-white/80">Evaluation:</strong></p>
                    <p>Code structure, naming conventions, and proper implementation will be assessed.</p>
                </div>
            </div>

            <!-- Editor Panel -->
            <div class="flex-1 flex flex-col bg-[#0D0D1E]">
                <!-- Editor Toolbar -->
                <div class="bg-[#111127] px-4 py-2.5 flex items-center justify-between border-b border-white/[0.06]">
                    <div class="flex items-center gap-2">
                        <select v-model="language" class="bg-white/[0.06] border border-white/10 text-white px-2.5 py-1 rounded-md font-mono text-xs outline-none cursor-pointer">
                            <option>PHP</option>
                            <option>JavaScript</option>
                            <option>Python</option>
                            <option>Java</option>
                            <option>C#</option>
                        </select>
                        <span class="text-xs text-white/30 font-mono">Solution.{{ language.toLowerCase() }}</span>
                    </div>
                    <div class="text-xs text-white/30">AI tools allowed ✓</div>
                </div>

                <!-- Code Editor -->
                <div class="flex-1 p-5 overflow-y-auto font-mono text-[13.5px] leading-[1.9] text-[#C8C8E8]">
                    <div
                        v-for="(line, index) in code.split('\n')"
                        :key="index"
                        class="flex gap-4 group"
                    >
                        <span class="text-gray-4 select-none min-w-[32px] text-right text-[12px] pt-px">{{ index + 1 }}</span>
                        <span class="flex-1">{{ line }}</span>
                    </div>
                </div>

                <!-- Submit Bar -->
                <div class="bg-[#111127] px-5 py-3 flex items-center justify-between border-t border-white/[0.06]">
                    <div class="flex gap-5">
                        <div class="text-xs text-white/40">
                            Keystrokes: <span class="text-white/80 font-semibold">{{ keystrokes }}</span>
                        </div>
                        <div class="text-xs text-white/40">
                            Pastes: <span class="text-white/80 font-semibold">{{ pastes }}</span>
                        </div>
                        <div class="text-xs text-white/40">
                            Lines: <span class="text-white/80 font-semibold">{{ lines }}</span>
                        </div>
                    </div>
                    <Link
                        :href="route('problems.index')"
                        class="bg-white/[0.06] border border-white/10 text-white/50 px-4 py-2 rounded-lg font-sans text-xs hover:text-white transition-colors"
                    >
                        ✕ Close Preview
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
