<?php

namespace App\Http\Resources\Admin;

use App\Models\Faq;
use App\Support\Translations;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Faq
 */
class FaqResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'question' => Translations::fill($this->resource, 'question'),
            'answer' => Translations::fill($this->resource, 'answer'),
        ];
    }
}
