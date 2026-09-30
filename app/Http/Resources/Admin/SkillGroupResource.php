<?php

namespace App\Http\Resources\Admin;

use App\Models\SkillGroup;
use App\Support\Translations;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SkillGroup
 */
class SkillGroupResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'label' => Translations::fill($this->resource, 'label'),
            'description' => Translations::fill($this->resource, 'description'),
            'skills' => $this->skills->pluck('name')->all(),
        ];
    }
}
