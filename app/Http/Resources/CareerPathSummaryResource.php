<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A career path as the nav and the home-page cards need it. Wraps the
 * config/portfolio.php entry plus its `key` (and optionally `years`).
 */
class CareerPathSummaryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'key' => $this->resource['key'],
            'number' => $this->resource['number'],
            'nav' => __($this->resource['nav']),
            'title' => __($this->resource['title']),
            'card_title' => __($this->resource['card_title']),
            'card' => __($this->resource['card']),
            'highlights' => $this->resource['highlights'],
            'years' => $this->resource['years'] ?? null,
        ];
    }
}
