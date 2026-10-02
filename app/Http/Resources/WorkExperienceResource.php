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
            // "2024-07" stamps for the home ledger; `to` is null while current.
            'from' => $this->start_date->format('Y-m'),
            'to' => $this->is_current || $this->end_date === null ? null : $this->end_date->format('Y-m'),
            'paths' => CareerPaths::forCompany($this->company),
        ];
    }
}
