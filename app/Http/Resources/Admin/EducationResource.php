<?php

namespace App\Http\Resources\Admin;

use App\Models\Education;
use App\Support\Translations;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Education
 */
class EducationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'institution' => $this->institution,
            'start_year' => $this->start_year,
            'end_year' => $this->end_year,
            'degree' => Translations::fill($this->resource, 'degree'),
            'location' => Translations::fill($this->resource, 'location'),
            'notes' => Translations::fill($this->resource, 'notes'),
        ];
    }
}
