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
            ],
        ]]);
    }
}
