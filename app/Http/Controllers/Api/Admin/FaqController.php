<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\MovesRecords;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FaqRequest;
use App\Http\Resources\Admin\FaqResource;
use App\Models\Faq;
use App\Models\User;
use App\Support\SortOrder;
use App\Support\Translations;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class FaqController extends Controller
{
    use MovesRecords;

    public function index(): AnonymousResourceCollection
    {
        return FaqResource::collection($this->records());
    }

    public function store(FaqRequest $request): JsonResponse
    {
        $user = User::firstOrFail();

        $faq = $user->faqs()->create($request->modelAttributes() + [
            'sort_order' => SortOrder::next($user->faqs()),
        ]);

        return (new FaqResource($faq))->response()->setStatusCode(201);
    }

    public function update(FaqRequest $request, Faq $faq): FaqResource
    {
        Translations::update($faq, $request->modelAttributes());

        return new FaqResource($faq);
    }

    public function destroy(Faq $faq): Response
    {
        $faq->delete();

        return response()->noContent();
    }

    public function move(Request $request, Faq $faq): AnonymousResourceCollection
    {
        $this->moveRecord($request, $faq);

        return $this->index();
    }

    /** @return Collection<int, Faq> */
    protected function records(): Collection
    {
        return User::firstOrFail()->faqs()->ordered()->get();
    }
}
