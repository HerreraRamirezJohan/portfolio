<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $user = User::withCount([
            'workExperiences', 'educations', 'skillGroups', 'languages', 'projects',
            'services', 'processSteps', 'faqs', 'costFactors',
        ])->firstOrFail();

        return response()->json(['data' => [
            'name' => $user->name,
            'email' => $user->email,
            'counts' => [
                'experiences' => $user->work_experiences_count,
                'educations' => $user->educations_count,
                'skill_groups' => $user->skill_groups_count,
                'languages' => $user->languages_count,
                'projects' => $user->projects_count,
                'services' => $user->services_count,
                'process_steps' => $user->process_steps_count,
                'faqs' => $user->faqs_count,
                'cost_factors' => $user->cost_factors_count,
            ],
        ]]);
    }
}
