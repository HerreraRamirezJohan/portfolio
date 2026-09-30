<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\MovesRecords;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LanguageRequest;
use App\Http\Resources\Admin\LanguageResource;
use App\Models\Language;
use App\Models\User;
use App\Support\SortOrder;
use App\Support\Translations;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class LanguageController extends Controller
{
    use MovesRecords;

    public function index(): AnonymousResourceCollection
    {
        return LanguageResource::collection($this->records());
    }

    public function store(LanguageRequest $request): JsonResponse
    {
        $user = User::firstOrFail();

        $language = $user->languages()->create($request->modelAttributes() + [
            'sort_order' => SortOrder::next($user->languages()),
        ]);

        return (new LanguageResource($language))->response()->setStatusCode(201);
    }

    public function update(LanguageRequest $request, Language $language): LanguageResource
    {
        Translations::update($language, $request->modelAttributes());

        return new LanguageResource($language);
    }

    public function destroy(Language $language): Response
    {
        $language->delete();

        return response()->noContent();
    }

    public function move(Request $request, Language $language): AnonymousResourceCollection
    {
        $this->moveRecord($request, $language);

        return $this->index();
    }

    /** @return Collection<int, Language> */
    protected function records(): Collection
    {
        return User::firstOrFail()->languages()->ordered()->get();
    }
}
