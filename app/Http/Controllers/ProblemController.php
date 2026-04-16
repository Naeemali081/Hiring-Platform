<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Problem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProblemController extends Controller
{
    public function index(Request $request): Response
    {
        $company = $this->getOrCreateCompany($request);
        $roleLevel = $request->string('role_level')->toString();
        $search = $request->string('search')->toString();

        $problems = Problem::query()
            ->where('company_id', $company->id)
            ->when($roleLevel, fn ($query) => $query->where('role_level', $roleLevel))
            ->when($search, fn ($query) => $query->where('title', 'like', "%{$search}%"))
            ->latest()
            ->get();

        $libraryProblems = Problem::query()
            ->where('is_public', true)
            ->whereNull('company_id')
            ->when($roleLevel, fn ($query) => $query->where('role_level', $roleLevel))
            ->when($search, fn ($query) => $query->where('title', 'like', "%{$search}%"))
            ->latest()
            ->get();

        return Inertia::render('Problems/Index', [
            'problems' => $problems,
            'libraryProblems' => $libraryProblems,
            'filters' => [
                'role_level' => $roleLevel,
                'search' => $search,
            ],
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(in_array($request->user()->role, ['company_admin', 'super_admin'], true), 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'difficulty' => ['required', 'in:easy,medium,hard'],
            'role_level' => ['required', 'in:junior,mid,senior'],
            'suggested_time_minutes' => ['required', 'integer', 'min:15', 'max:240'],
            'tags' => ['nullable', 'string'],
            'is_public' => ['boolean'],
        ]);

        $company = $this->getOrCreateCompany($request);

        Problem::create([
            ...$data,
            'tags' => collect(explode(',', $data['tags'] ?? ''))
                ->map(fn (string $tag) => trim($tag))
                ->filter()
                ->values()
                ->all(),
            'is_public' => $data['is_public'] ?? false,
            'company_id' => $company->id,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('problems.index')->with('success', 'Problem created successfully.');
    }

    public function preview(Problem $problem)
    {
        return Inertia::render('Problems/Preview', [
            'problem' => $problem,
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
