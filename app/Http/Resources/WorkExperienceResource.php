<?php

namespace App\Http\Resources;

use App\Models\WorkExperience;
use App\Support\CareerPaths;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin WorkExperience
 */
class WorkExperienceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company' => $this->company,
            'role' => $this->role,
            'range' => $this->compactRange(),
            'paths' => CareerPaths::forCompany($this->company),
        ];
    }
}
