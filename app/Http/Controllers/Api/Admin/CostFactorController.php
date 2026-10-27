<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\MovesRecords;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CostFactorRequest;
use App\Http\Resources\Admin\CostFactorResource;
use App\Models\CostFactor;
use App\Models\User;
use App\Support\SortOrder;
use App\Support\Translations;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class CostFactorController extends Controller
{
    use MovesRecords;

    public function index(): AnonymousResourceCollection
    {
        return CostFactorResource::collection($this->records());
    }

    public function store(CostFactorRequest $request): JsonResponse
    {
        $user = User::firstOrFail();

        $factor = $user->costFactors()->create($request->modelAttributes() + [
            'sort_order' => SortOrder::next($user->costFactors()),
        ]);

        return (new CostFactorResource($factor))->response()->setStatusCode(201);
    }

    public function update(CostFactorRequest $request, CostFactor $costFactor): CostFactorResource
    {
        Translations::update($costFactor, $request->modelAttributes());

        return new CostFactorResource($costFactor);
    }

    public function destroy(CostFactor $costFactor): Response
    {
        $costFactor->delete();

        return response()->noContent();
    }

    public function move(Request $request, CostFactor $costFactor): AnonymousResourceCollection
    {
        $this->moveRecord($request, $costFactor);

        return $this->index();
    }

    /** @return Collection<int, CostFactor> */
    protected function records(): Collection
    {
        return User::firstOrFail()->costFactors()->ordered()->get();
    }
}
