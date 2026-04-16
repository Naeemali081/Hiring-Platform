<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Company;
use App\Models\Submission;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function index(Request $request): Response
    {
        $company = $this->getOrCreateCompany($request);

        $reports = Assessment::query()
            ->where('company_id', $company->id)
            ->with(['problem:id,title', 'submission'])
            ->whereHas('submission')
            ->latest()
            ->get()
            ->map(function (Assessment $assessment) {
                $submission = $assessment->submission;
                $totalChars = $submission->manual_chars + $submission->pasted_chars;
                $pasteRate = $totalChars > 0 ? (int) round(($submission->pasted_chars / $totalChars) * 100) : 0;

                return [
                    'id' => $assessment->id,
                    'candidate_name' => $assessment->candidate_name,
                    'candidate_email' => $assessment->candidate_email,
                    'role_level' => $assessment->role_level,
                    'status' => $assessment->status,
                    'problem_title' => $assessment->problem?->title,
                    'score' => $submission->score,
                    'paste_rate' => $pasteRate,
                    'tab_switches' => $submission->tab_switches,
                    'defend_outcome' => $submission->defend_outcome,
                ];
            });

        return Inertia::render('Reports/Index', [
            'reports' => $reports,
            'summary' => [
                'average_score' => (int) round(Submission::whereHas('assessment', fn ($query) => $query->where('company_id', $company->id))->avg('score') ?? 0),
                'average_tab_switches' => (int) round(Submission::whereHas('assessment', fn ($query) => $query->where('company_id', $company->id))->avg('tab_switches') ?? 0),
                'pass_count' => Submission::whereHas('assessment', fn ($query) => $query->where('company_id', $company->id))->where('defend_outcome', 'pass')->count(),
            ],
        ]);
    }

    public function show(Request $request, Assessment $assessment): Response
    {
        $company = $this->getOrCreateCompany($request);
        abort_unless($assessment->company_id === $company->id, 403);

        $assessment->load(['problem', 'submission']);
        abort_unless($assessment->submission, 404);
        $submission = $assessment->submission;
        $totalChars = $submission->manual_chars + $submission->pasted_chars;

        return Inertia::render('Reports/Show', [
            'assessment' => $assessment,
            'report' => [
                'manual_percentage' => $totalChars > 0 ? (int) round(($submission->manual_chars / $totalChars) * 100) : 0,
                'paste_percentage' => $totalChars > 0 ? (int) round(($submission->pasted_chars / $totalChars) * 100) : 0,
                'time_taken_minutes' => $assessment->started_at && $assessment->submitted_at
                    ? $assessment->started_at->diffInMinutes($assessment->submitted_at)
                    : null,
                'tab_switch_timestamps' => $submission->tab_switch_timestamps ?? [],
            ],
        ]);
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
