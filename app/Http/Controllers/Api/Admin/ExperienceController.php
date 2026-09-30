<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\MovesRecords;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ExperienceRequest;
use App\Http\Resources\Admin\ExperienceResource;
use App\Models\User;
use App\Models\WorkExperience;
use App\Support\SortOrder;
use App\Support\Translations;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class ExperienceController extends Controller
{
    use MovesRecords;

    public function index(): AnonymousResourceCollection
    {
        return ExperienceResource::collection($this->records());
    }

    public function store(ExperienceRequest $request): JsonResponse
    {
        $user = User::firstOrFail();

        $experience = $user->workExperiences()->create($request->modelAttributes() + [
            'sort_order' => SortOrder::next($user->workExperiences()),
        ]);

        return (new ExperienceResource($experience))->response()->setStatusCode(201);
    }

    public function update(ExperienceRequest $request, WorkExperience $experience): ExperienceResource
    {
        Translations::update($experience, $request->modelAttributes());

        return new ExperienceResource($experience);
    }

    public function destroy(WorkExperience $experience): Response
    {
        $experience->delete();

        return response()->noContent();
    }

    public function move(Request $request, WorkExperience $experience): AnonymousResourceCollection
    {
        $this->moveRecord($request, $experience);

        return $this->index();
    }

    /** @return Collection<int, WorkExperience> */
    protected function records(): Collection
    {
        return User::firstOrFail()->workExperiences()->ordered()->get();
    }
}
