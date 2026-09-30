<?php

namespace App\Http\Resources\Admin;

use App\Models\Language;
use App\Support\Translations;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Language
 */
class LanguageResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => Translations::fill($this->resource, 'name'),
            'level_label' => Translations::fill($this->resource, 'level_label'),
        ];
    }
}
