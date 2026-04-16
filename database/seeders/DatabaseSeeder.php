<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Company;
use App\Models\Problem;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $company = Company::updateOrCreate(
            ['email' => 'admin@braincode360.test'],
            [
                'name' => 'BrainCode Hiring Labs',
                'plan' => 'pro',
                'plan_expires_at' => now()->addMonth(),
                'total_assessments' => 3,
            ],
        );

        $admin = User::updateOrCreate([
            'email' => 'admin@braincode360.test',
        ], [
            'name' => 'Company Admin',
            'password' => Hash::make('password'),
            'role' => 'company_admin',
            'company_id' => $company->id,
            'email_verified_at' => now(),
        ]);

        User::updateOrCreate([
            'email' => 'interviewer@braincode360.test',
        ], [
            'name' => 'Technical Interviewer',
            'password' => Hash::make('password'),
            'role' => 'interviewer',
            'company_id' => $company->id,
            'email_verified_at' => now(),
        ]);

        $publicProblems = [
            [
                'title' => 'Build a rate-limited Laravel API endpoint',
                'description' => 'Create a secure endpoint with validation, pagination, and rate limiting.',
                'difficulty' => 'medium',
                'role_level' => 'mid',
                'suggested_time_minutes' => 75,
                'tags' => ['Laravel', 'API', 'Security'],
            ],
            [
                'title' => 'Design a Vue dashboard table with filters',
                'description' => 'Implement a sortable table with search, status filters, and empty states.',
                'difficulty' => 'easy',
                'role_level' => 'junior',
                'suggested_time_minutes' => 60,
                'tags' => ['Vue', 'UX'],
            ],
            [
                'title' => 'Refactor a job queue workflow',
                'description' => 'Improve reliability, retries, and observability for queued jobs.',
                'difficulty' => 'hard',
                'role_level' => 'senior',
                'suggested_time_minutes' => 90,
                'tags' => ['Architecture', 'Queues'],
            ],
        ];

        foreach ($publicProblems as $problem) {
            Problem::updateOrCreate(
                ['title' => $problem['title'], 'company_id' => null],
                [
                    ...$problem,
                    'company_id' => null,
                    'is_public' => true,
                    'created_by' => $admin->id,
                ],
            );
        }

        $companyProblem = Problem::updateOrCreate(
            ['title' => 'Implement candidate activity transparency report', 'company_id' => $company->id],
            [
                'description' => 'Track manual typing, paste activity, and tab switching, then display a reviewable report.',
                'difficulty' => 'medium',
                'role_level' => 'mid',
                'suggested_time_minutes' => 80,
                'tags' => ['Reporting', 'Laravel', 'Vue'],
                'is_public' => false,
                'created_by' => $admin->id,
            ],
        );

        $assessment = Assessment::updateOrCreate(
            ['candidate_email' => 'candidate1@example.com', 'company_id' => $company->id],
            [
                'problem_id' => $companyProblem->id,
                'candidate_name' => 'Sara Khan',
                'role_level' => 'mid',
                'token' => Str::random(64),
                'time_limit_minutes' => 90,
                'status' => 'reviewed',
                'started_at' => now()->subHours(3),
                'submitted_at' => now()->subHours(2),
                'expires_at' => now()->addDays(5),
                'defend_scheduled_at' => now()->addDays(2),
            ],
        );

        Submission::updateOrCreate(
            ['assessment_id' => $assessment->id],
            [
                'code' => "<?php\n\n// Candidate submission placeholder\nreturn ['status' => 'ok'];\n",
                'language' => 'php',
                'execution_result' => [
                    'stdout' => 'All tests passed',
                    'status' => ['description' => 'Accepted'],
                ],
                'manual_chars' => 1820,
                'pasted_chars' => 340,
                'paste_events' => 3,
                'tab_switches' => 2,
                'tab_switch_timestamps' => [
                    now()->subHours(2)->subMinutes(40)->toDateTimeString(),
                    now()->subHours(2)->subMinutes(5)->toDateTimeString(),
                ],
                'keystrokes_log' => [
                    ['type' => 'manual', 'chars' => 120],
                    ['type' => 'paste', 'chars' => 50],
                ],
                'score' => 84,
                'reviewer_notes' => "Strong implementation overall.\nNeeds small improvement in edge-case handling.",
                'defend_outcome' => 'pass',
            ],
        );

        $pendingAssessment = Assessment::updateOrCreate(
            ['candidate_email' => 'candidate2@example.com', 'company_id' => $company->id],
            [
                'problem_id' => $companyProblem->id,
                'candidate_name' => 'Ali Raza',
                'role_level' => 'mid',
                'token' => Str::random(64),
                'time_limit_minutes' => 75,
                'status' => 'in_progress',
                'started_at' => now()->subMinutes(20),
                'submitted_at' => null,
                'expires_at' => now()->addDays(7),
                'defend_scheduled_at' => null,
            ],
        );

        Submission::updateOrCreate(
            ['assessment_id' => $pendingAssessment->id],
            [
                'code' => "// Work in progress\nconst result = [];\n",
                'language' => 'javascript',
                'execution_result' => [
                    'stdout' => '2 tests failing',
                    'status' => ['description' => 'Runtime Error'],
                ],
                'manual_chars' => 900,
                'pasted_chars' => 210,
                'paste_events' => 2,
                'tab_switches' => 1,
                'tab_switch_timestamps' => [
                    now()->subMinutes(9)->toDateTimeString(),
                ],
                'keystrokes_log' => [
                    ['type' => 'manual', 'chars' => 80],
                ],
                'score' => null,
                'reviewer_notes' => null,
                'defend_outcome' => null,
            ],
        );

        Assessment::updateOrCreate([
            'candidate_email' => 'candidate3@example.com',
            'company_id' => $company->id,
        ], [
            'problem_id' => $companyProblem->id,
            'candidate_name' => 'Hina Malik',
            'role_level' => 'senior',
            'token' => Str::random(64),
            'time_limit_minutes' => 120,
            'status' => 'pending',
            'expires_at' => now()->addDays(10),
        ]);
    }
}
