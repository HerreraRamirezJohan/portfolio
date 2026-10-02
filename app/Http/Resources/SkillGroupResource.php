<?php

namespace App\Http\Resources;

use App\Models\Skill;
use App\Models\SkillGroup;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * One column of the home page's stack index: a translated label and the
 * skill names, which are proper nouns and never translated.
 *
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
            'label' => $this->label,
            'skills' => $this->skills->map(fn (Skill $skill) => $skill->name)->values(),
        ];
    }
}
