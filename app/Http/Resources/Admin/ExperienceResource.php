<?php

namespace App\Http\Resources\Admin;

use App\Models\WorkExperience;
use App\Support\Translations;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin WorkExperience
 */
class ExperienceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company' => $this->company,
            'start_date' => $this->start_date->toDateString(),
            'end_date' => $this->end_date?->toDateString() ?? '',
            'is_current' => $this->is_current,
            'date_range' => $this->dateRange(),
            'role' => Translations::fill($this->resource, 'role'),
            'location' => Translations::fill($this->resource, 'location'),
            'bullets' => Translations::fill($this->resource, 'bullets', []),
        ];
    }
}
