<?php

namespace App\Http\Resources;

use App\Models\Service;
use App\Support\HourlyRates;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Service
 */
class ServiceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'path' => $this->path,
            'title' => $this->title,
            'summary' => $this->summary ?: null,
            // Short topic tags (UI/UX, testing...), one per line in the admin.
            'topics' => $this->deliverables ?: [],
            // The hourly rate in the active locale's currency.
            'rate' => $this->rate_usd === null
                ? null
                : app(HourlyRates::class)->forLocale($this->rate_usd, app()->getLocale()),
        ];
    }
}
