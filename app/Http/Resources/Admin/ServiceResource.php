<?php

namespace App\Http\Resources\Admin;

use App\Models\Service;
use App\Support\Translations;
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
            'path' => $this->path ?? '',
            'rate_usd' => $this->rate_usd,
            'is_published' => $this->is_published,
            'title' => Translations::fill($this->resource, 'title'),
            'summary' => Translations::fill($this->resource, 'summary'),
            'deliverables' => Translations::fill($this->resource, 'deliverables', []),
        ];
    }
}
