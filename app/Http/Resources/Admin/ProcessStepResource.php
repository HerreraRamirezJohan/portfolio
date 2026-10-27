<?php

namespace App\Http\Resources\Admin;

use App\Models\ProcessStep;
use App\Support\Translations;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ProcessStep
 */
class ProcessStepResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => Translations::fill($this->resource, 'title'),
            'body' => Translations::fill($this->resource, 'body'),
        ];
    }
}
