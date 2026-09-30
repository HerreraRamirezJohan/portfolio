<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\MovesRecords;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EducationRequest;
use App\Http\Resources\Admin\EducationResource;
use App\Models\Education;
use App\Models\User;
use App\Support\SortOrder;
use App\Support\Translations;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class EducationController extends Controller
{
    use MovesRecords;

    public function index(): AnonymousResourceCollection
    {
        return EducationResource::collection($this->records());
    }

    public function store(EducationRequest $request): JsonResponse
    {
        $user = User::firstOrFail();

        $education = $user->educations()->create($request->modelAttributes() + [
            'sort_order' => SortOrder::next($user->educations()),
        ]);

        return (new EducationResource($education))->response()->setStatusCode(201);
    }

    public function update(EducationRequest $request, Education $education): EducationResource
    {
        Translations::update($education, $request->modelAttributes());

        return new EducationResource($education);
    }

    public function destroy(Education $education): Response
    {
        $education->delete();

        return response()->noContent();
    }

    public function move(Request $request, Education $education): AnonymousResourceCollection
    {
        $this->moveRecord($request, $education);

        return $this->index();
    }

    /** @return Collection<int, Education> */
    protected function records(): Collection
    {
        return User::firstOrFail()->educations()->ordered()->get();
    }
}
