<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\MovesRecords;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceRequest;
use App\Http\Resources\Admin\ServiceResource;
use App\Models\Service;
use App\Models\User;
use App\Support\SortOrder;
use App\Support\Translations;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class ServiceController extends Controller
{
    use MovesRecords;

    public function index(): AnonymousResourceCollection
    {
        return ServiceResource::collection($this->records());
    }

    public function store(ServiceRequest $request): JsonResponse
    {
        $user = User::firstOrFail();

        $service = $user->services()->create($request->modelAttributes() + [
            'sort_order' => SortOrder::next($user->services()),
        ]);

        return (new ServiceResource($service))->response()->setStatusCode(201);
    }

    public function update(ServiceRequest $request, Service $service): ServiceResource
    {
        Translations::update($service, $request->modelAttributes());

        return new ServiceResource($service);
    }

    public function destroy(Service $service): Response
    {
        $service->delete();

        return response()->noContent();
    }

    public function move(Request $request, Service $service): AnonymousResourceCollection
    {
        $this->moveRecord($request, $service);

        return $this->index();
    }

    /** @return Collection<int, Service> */
    protected function records(): Collection
    {
        return User::firstOrFail()->services()->ordered()->get();
    }
}
