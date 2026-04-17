<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CandidateController extends Controller
{
    private function getAssessmentByToken(string $token)
    {
        return Assessment::with('problem')->where('token', $token)->first();
    }

    public function show(string $token)
    {
        $assessment = $this->getAssessmentByToken($token);

        if (!$assessment) {
            return response()->json(['error' => 'Invalid token.'], 404);
        }

        if ($assessment->expires_at && now()->isAfter($assessment->expires_at)) {
            return response()->json(['error' => 'Assessment has expired.'], 403);
        }

        if (in_array($assessment->status, ['submitted', 'reviewed'])) {
            return response()->json(['error' => 'Assessment already completed.'], 403);
        }

        return response()->json([
            'assessment' => [
                'candidate_name' => $assessment->candidate_name,
                'role_level' => $assessment->role_level,
                'time_limit_minutes' => $assessment->time_limit_minutes,
                'status' => $assessment->status,
                'started_at' => $assessment->started_at,
                'problem' => [
                    'title' => $assessment->problem->title,
                    'description' => $assessment->problem->description,
                ]
            ]
        ]);
    }

    public function start(string $token)
    {
        $assessment = $this->getAssessmentByToken($token);
        if (!$assessment || $assessment->status !== 'pending') {
            return response()->json(['error' => 'Cannot start assessment.'], 400);
        }

        $assessment->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'started_at' => $assessment->started_at
        ]);
    }

    public function activity(Request $request, string $token)
    {
        // Silent tracking - we could buffer this to Redis or echo directly to Reverb
        // For now, we will just return OK to avoid blocking.
        return response()->json(['success' => true]);
    }

    public function submit(Request $request, string $token)
    {
        $assessment = $this->getAssessmentByToken($token);
        
        if (!$assessment || !in_array($assessment->status, ['pending', 'in_progress'])) {
            return response()->json(['error' => 'Invalid state for submission.'], 400);
        }

        $validated = $request->validate([
            'code' => 'required|string',
            'language' => 'required|string',
            'manual_chars' => 'integer',
            'pasted_chars' => 'integer',
            'paste_events' => 'integer',
            'tab_switches' => 'integer',
        ]);

        Submission::create([
            'assessment_id' => $assessment->id,
            'code' => $validated['code'],
            'language' => $validated['language'],
            'manual_chars' => $validated['manual_chars'] ?? 0,
            'pasted_chars' => $validated['pasted_chars'] ?? 0,
            'paste_events' => $validated['paste_events'] ?? 0,
            'tab_switches' => $validated['tab_switches'] ?? 0,
            'keystrokes_log' => '[]', // Should be S3 path in real production
        ]);

        $assessment->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }
}
