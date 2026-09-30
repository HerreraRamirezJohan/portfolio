<?php

namespace App\Http\Requests\Admin;

use App\Support\Translations;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'slug' => [
                'required', 'string', 'max:255', 'alpha_dash',
                Rule::unique('projects', 'slug')->ignore($this->route('project')),
            ],
            'repo_url' => 'nullable|url|max:255',
            'live_url' => 'nullable|url|max:255',
            'is_published' => 'boolean',
            'year' => 'nullable|string|max:32',
            'tech_stack' => 'nullable|array',
            'tech_stack.*' => 'nullable|string|max:100',
            ...Translations::rules('title'),
            ...Translations::rules('summary', 1000),
            ...Translations::rules('description', 5000),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function modelAttributes(): array
    {
        $stack = collect($this->input('tech_stack', []))
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values()
            ->all();

        return [
            'slug' => $this->input('slug'),
            'repo_url' => $this->input('repo_url') ?: null,
            'live_url' => $this->input('live_url') ?: null,
            'is_published' => $this->boolean('is_published'),
            'year' => $this->input('year') ?: null,
            'tech_stack' => $stack ?: null,
            'title' => Translations::clean($this->input('title')),
            'summary' => Translations::clean($this->input('summary')),
            'description' => Translations::clean($this->input('description')),
        ];
    }
}
