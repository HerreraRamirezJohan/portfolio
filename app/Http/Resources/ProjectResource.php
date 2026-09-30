<?php

namespace App\Http\Resources;

use App\Models\Project;
use App\Support\TechIcon;
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
            'title' => $this->title,
            'summary' => $this->summary ?: null,
            'description' => $this->description ?: null,
            'year' => $this->year,
            'repo_url' => $this->repo_url,
            'live_url' => $this->live_url,
            'tech_stack' => TechIcon::chips($this->tech_stack),
        ];
    }
}
