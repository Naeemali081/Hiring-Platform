<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Problem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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

    public function suggestTime(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'difficulty' => 'required|string',
            'role_level' => 'required|string',
        ]);

        $apiKey = env('ANTHROPIC_API_KEY');

        // Graceful fallback if no API key is provided
        if (!$apiKey) {
            Log::info('AI Time Suggestion mock used because ANTHROPIC_API_KEY is missing.');
            $baseTime = match($request->role_level) {
                'junior' => 45,
                'mid' => 60,
                'senior' => 90,
                default => 60,
            };
            
            // Make the mock deterministic based on the description length so the same text always returns the exact same time
            $complexityOffset = (strlen($request->description) % 15) - 5;
            $mockTime = $baseTime + $complexityOffset;
            
            return response()->json(['suggested_minutes' => $mockTime]);
        }

        try {
            $prompt = sprintf(
                "You are an expert technical recruiter and senior engineering manager. " .
                "Evaluate this programming assessment:\n\nTitle: %s\nRole Level Requirement: %s\nTarget Difficulty: %s\n\nProblem Description:\n%s\n\n" .
                "Based on this, what is the exact recommended time limit in minutes for a %s developer to solve this optimally under interview conditions? " .
                "Respond ONLY with a single integer representing the minutes. (For example: 45)",
                $request->title,
                $request->role_level,
                $request->difficulty,
                $request->description,
                $request->role_level
            );

            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-3-haiku-20240307',
                'max_tokens' => 10,
                'temperature' => 0.0, // Ensures deterministic output for the exact same problem prompt
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ]
            ]);

            if ($response->successful()) {
                $content = $response->json('content.0.text', '60');
                // Extract only numbers just in case the AI added extra text
                $minutes = preg_replace('/[^0-9]/', '', $content);
                
                return response()->json([
                    'suggested_minutes' => !empty($minutes) ? (int)$minutes : 60
                ]);
            }

            Log::error('Anthropic API Error:', $response->json());
            return response()->json(['suggested_minutes' => 60], 500);

        } catch (\Exception $e) {
            Log::error('AI Suggest Time Exception: ' . $e->getMessage());
            return response()->json(['suggested_minutes' => 60], 500);
        }
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
