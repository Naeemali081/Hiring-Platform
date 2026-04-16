<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Company;
use App\Models\Problem;
use App\Models\Submission;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $company = $this->getOrCreateCompany($request);

        $assessments = Assessment::query()
            ->where('company_id', $company->id)
            ->with(['problem:id,title', 'submission'])
            ->latest()
            ->take(5)
            ->get();

        $companyAssessments = Assessment::where('company_id', $company->id);
        $submittedAssessments = Assessment::where('company_id', $company->id)->whereIn('status', ['submitted', 'reviewed']);
        $submissions = Submission::query()
            ->whereHas('assessment', fn ($query) => $query->where('company_id', $company->id));

        $averageScore = (int) round((clone $submissions)->whereNotNull('score')->avg('score') ?? 0);
        $averagePasteRate = (int) round((clone $submissions)
            ->get()
            ->avg(function (Submission $submission) {
                $total = $submission->manual_chars + $submission->pasted_chars;

                return $total > 0 ? ($submission->pasted_chars / $total) * 100 : 0;
            }) ?? 0);

        return Inertia::render('Dashboard', [
            'company' => $company,
            'stats' => [
                'problems' => Problem::where('company_id', $company->id)->count(),
                'assessments' => (clone $companyAssessments)->count(),
                'pending' => (clone $companyAssessments)->where('status', 'pending')->count(),
                'submitted' => (clone $companyAssessments)->where('status', 'submitted')->count(),
                'in_progress' => (clone $companyAssessments)->where('status', 'in_progress')->count(),
                'reviewed' => (clone $companyAssessments)->where('status', 'reviewed')->count(),
                'average_score' => $averageScore,
                'pass_rate' => (int) round(((clone $submissions)->where('defend_outcome', 'pass')->count() / max((clone $submittedAssessments)->count(), 1)) * 100),
                'average_paste_rate' => $averagePasteRate,
            ],
            'recentAssessments' => $assessments,
            'roleBreakdown' => [
                'junior' => (clone $companyAssessments)->where('role_level', 'junior')->count(),
                'mid' => (clone $companyAssessments)->where('role_level', 'mid')->count(),
                'senior' => (clone $companyAssessments)->where('role_level', 'senior')->count(),
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
