<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\MovesRecords;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SkillGroupRequest;
use App\Http\Resources\Admin\SkillGroupResource;
use App\Models\SkillGroup;
use App\Models\User;
use App\Support\SortOrder;
use App\Support\Translations;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SkillGroupController extends Controller
{
    use MovesRecords;

    public function index(): AnonymousResourceCollection
    {
        return SkillGroupResource::collection($this->records());
    }

    public function store(SkillGroupRequest $request): JsonResponse
    {
        $user = User::firstOrFail();

        $group = DB::transaction(function () use ($request, $user) {
            $group = $user->skillGroups()->create($request->modelAttributes() + [
                'sort_order' => SortOrder::next($user->skillGroups()),
            ]);

            $this->syncSkills($group, $request->skillNames());

            return $group;
        });

        return (new SkillGroupResource($group->load('skills')))->response()->setStatusCode(201);
    }

    public function update(SkillGroupRequest $request, SkillGroup $skillGroup): SkillGroupResource
    {
        DB::transaction(function () use ($request, $skillGroup) {
            Translations::update($skillGroup, $request->modelAttributes());
            $this->syncSkills($skillGroup, $request->skillNames());
        });

        return new SkillGroupResource($skillGroup->load('skills'));
    }

    public function destroy(SkillGroup $skillGroup): Response
    {
        $skillGroup->delete();

        return response()->noContent();
    }

    public function move(Request $request, SkillGroup $skillGroup): AnonymousResourceCollection
    {
        $this->moveRecord($request, $skillGroup);

        return $this->index();
    }

    /** @return Collection<int, SkillGroup> */
    protected function records(): Collection
    {
        return User::firstOrFail()->skillGroups()->with('skills')->ordered()->get();
    }

    /**
     * Replace the skill list wholesale -- simplest correct behaviour for a
     * short, hand-edited list, and keeps sort_order contiguous.
     *
     * @param  array<int, string>  $names
     */
    private function syncSkills(SkillGroup $group, array $names): void
    {
        $group->skills()->delete();

        foreach ($names as $index => $name) {
            $group->skills()->create(['name' => $name, 'sort_order' => $index + 1]);
        }
    }
}
