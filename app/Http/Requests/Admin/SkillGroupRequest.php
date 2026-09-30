<?php

namespace App\Http\Requests\Admin;

use App\Support\Translations;
use Illuminate\Foundation\Http\FormRequest;

class SkillGroupRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            ...Translations::rules('label'),
            ...Translations::rules('description', 2000),
            // Skill names are not translated -- they are proper nouns (PHP, React).
            'skills' => 'nullable|array',
            'skills.*' => 'nullable|string|max:100',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function modelAttributes(): array
    {
        return [
            'label' => Translations::clean($this->input('label')),
            'description' => Translations::clean($this->input('description')),
        ];
    }

    /**
     * @return array<int, string>
     */
    public function skillNames(): array
    {
        return collect($this->input('skills', []))
            ->map(fn ($name) => trim((string) $name))
            ->filter()
            ->values()
            ->all();
    }
}
