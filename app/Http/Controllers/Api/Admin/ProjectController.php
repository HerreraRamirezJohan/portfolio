<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\MovesRecords;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectRequest;
use App\Http\Resources\Admin\ProjectResource;
use App\Models\Project;
use App\Models\User;
use App\Support\SortOrder;
use App\Support\Translations;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class ProjectController extends Controller
{
    use MovesRecords;

    public function index(): AnonymousResourceCollection
    {
        return ProjectResource::collection($this->records());
    }

    public function store(ProjectRequest $request): JsonResponse
    {
        $user = User::firstOrFail();

        $project = $user->projects()->create($request->modelAttributes() + [
            'sort_order' => SortOrder::next($user->projects()),
        ]);

        return (new ProjectResource($project))->response()->setStatusCode(201);
    }

    public function update(ProjectRequest $request, Project $project): ProjectResource
    {
        Translations::update($project, $request->modelAttributes());

        return new ProjectResource($project);
    }

    public function destroy(Project $project): Response
    {
        $project->delete();

        return response()->noContent();
    }

    public function move(Request $request, Project $project): AnonymousResourceCollection
    {
        $this->moveRecord($request, $project);

        return $this->index();
    }

    /** @return Collection<int, Project> */
    protected function records(): Collection
    {
        return User::firstOrFail()->projects()->ordered()->get();
    }
}
