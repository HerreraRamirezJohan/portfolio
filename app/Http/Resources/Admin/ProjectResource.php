<?php

namespace App\Http\Resources\Admin;

use App\Models\Project;
use App\Support\Translations;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Project
 */
class ProjectResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'repo_url' => $this->repo_url ?? '',
            'live_url' => $this->live_url ?? '',
            'is_published' => $this->is_published,
            'year' => $this->year ?? '',
            'tech_stack' => $this->tech_stack ?? [],
            'title' => Translations::fill($this->resource, 'title'),
            'summary' => Translations::fill($this->resource, 'summary'),
            'description' => Translations::fill($this->resource, 'description'),
        ];
    }
}
