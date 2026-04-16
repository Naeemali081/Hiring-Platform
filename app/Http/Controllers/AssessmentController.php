<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Company;
use App\Models\Problem;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AssessmentController extends Controller
{
    public function index(Request $request): Response
    {
        $company = $this->getOrCreateCompany($request);
        $status = $request->string('status')->toString();

        $assessments = Assessment::query()
            ->where('company_id', $company->id)
            ->with(['problem:id,title', 'submission'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->get();

        $problems = Problem::query()
            ->where('company_id', $company->id)
            ->orderBy('title')
            ->get(['id', 'title', 'role_level', 'difficulty', 'suggested_time_minutes']);

        return Inertia::render('Assessments/Index', [
            'assessments' => $assessments,
            'problems' => $problems,
            'filters' => [
                'status' => $status,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(in_array($request->user()->role, ['company_admin', 'super_admin'], true), 403);

        $data = $request->validate([
            'problem_id' => ['required', 'exists:problems,id'],
            'candidate_name' => ['required', 'string', 'max:255'],
            'candidate_email' => ['required', 'email', 'max:255'],
            'role_level' => ['required', 'in:junior,mid,senior'],
            'time_limit_minutes' => ['required', 'integer', 'min:15', 'max:240'],
            'expires_in_days' => ['required', 'integer', 'min:1', 'max:30'],
        ]);

        $company = $this->getOrCreateCompany($request);

        Assessment::create([
            'problem_id' => $data['problem_id'],
            'candidate_name' => $data['candidate_name'],
            'candidate_email' => $data['candidate_email'],
            'role_level' => $data['role_level'],
            'time_limit_minutes' => $data['time_limit_minutes'],
            'company_id' => $company->id,
            'token' => Str::random(64),
            'expires_at' => now()->addDays($data['expires_in_days']),
        ]);

        $company->increment('total_assessments');

        return redirect()->route('assessments.index')->with('success', 'Assessment created and sent.');
    }

    public function show(Request $request, Assessment $assessment): Response
    {
        $company = $this->getOrCreateCompany($request);
        abort_unless($assessment->company_id === $company->id, 403);

        $assessment->load(['problem', 'submission']);

        return Inertia::render('Assessments/Show', [
            'assessment' => $assessment,
            'activity' => [
                'manual_percentage' => $assessment->submission
                    ? $this->manualPercentage($assessment->submission)
                    : 0,
                'paste_percentage' => $assessment->submission
                    ? $this->pastePercentage($assessment->submission)
                    : 0,
            ],
        ]);
    }

    public function update(Request $request, Assessment $assessment): RedirectResponse
    {
        $company = $this->getOrCreateCompany($request);
        abort_unless($assessment->company_id === $company->id, 403);

        $data = $request->validate([
            'status' => ['required', 'in:pending,in_progress,submitted,reviewed'],
            'defend_scheduled_at' => ['nullable', 'date'],
            'score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'reviewer_notes' => ['nullable', 'string'],
            'defend_outcome' => ['nullable', 'in:pass,fail,undecided'],
        ]);

        $assessment->update([
            'status' => $data['status'],
            'defend_scheduled_at' => $data['defend_scheduled_at'] ?? $assessment->defend_scheduled_at,
            'submitted_at' => in_array($data['status'], ['submitted', 'reviewed'], true)
                ? ($assessment->submitted_at ?? now())
                : $assessment->submitted_at,
        ]);

        if ($assessment->submission) {
            $assessment->submission->update([
                'score' => $data['score'] ?? $assessment->submission->score,
                'reviewer_notes' => $data['reviewer_notes'] ?? $assessment->submission->reviewer_notes,
                'defend_outcome' => $data['defend_outcome'] ?? $assessment->submission->defend_outcome,
            ]);
        }

        return redirect()->route('assessments.show', $assessment)->with('success', 'Assessment updated.');
    }

    private function manualPercentage(Submission $submission): int
    {
        $total = $submission->manual_chars + $submission->pasted_chars;

        return $total > 0 ? (int) round(($submission->manual_chars / $total) * 100) : 0;
    }

    private function pastePercentage(Submission $submission): int
    {
        $total = $submission->manual_chars + $submission->pasted_chars;

        return $total > 0 ? (int) round(($submission->pasted_chars / $total) * 100) : 0;
    }

    private function getOrCreateCompany(Request $request): Company
    {
        $user = $request->user();

        if ($user->company_id) {
            return $user->company;
        }

        $company = Company::create([
            'name' => "{$user->name}'s Company",
            'email' => $user->email,
        ]);

        $user->update(['company_id' => $company->id]);

        return $company;
    }
}
