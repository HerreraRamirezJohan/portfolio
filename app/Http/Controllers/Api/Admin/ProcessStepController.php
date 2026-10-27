<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\MovesRecords;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProcessStepRequest;
use App\Http\Resources\Admin\ProcessStepResource;
use App\Models\ProcessStep;
use App\Models\User;
use App\Support\SortOrder;
use App\Support\Translations;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class ProcessStepController extends Controller
{
    use MovesRecords;

    public function index(): AnonymousResourceCollection
    {
        return ProcessStepResource::collection($this->records());
    }

    public function store(ProcessStepRequest $request): JsonResponse
    {
        $user = User::firstOrFail();

        $step = $user->processSteps()->create($request->modelAttributes() + [
            'sort_order' => SortOrder::next($user->processSteps()),
        ]);

        return (new ProcessStepResource($step))->response()->setStatusCode(201);
    }

    public function update(ProcessStepRequest $request, ProcessStep $processStep): ProcessStepResource
    {
        Translations::update($processStep, $request->modelAttributes());

        return new ProcessStepResource($processStep);
    }

    public function destroy(ProcessStep $processStep): Response
    {
        $processStep->delete();

        return response()->noContent();
    }

    public function move(Request $request, ProcessStep $processStep): AnonymousResourceCollection
    {
        $this->moveRecord($request, $processStep);

        return $this->index();
    }

    /** @return Collection<int, ProcessStep> */
    protected function records(): Collection
    {
        return User::firstOrFail()->processSteps()->ordered()->get();
    }
}
