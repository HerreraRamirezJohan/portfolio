<?php

namespace App\Http\Requests\Admin;

use App\Support\CareerPaths;
use App\Support\Translations;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'slug' => [
                'required', 'string', 'max:255', 'alpha_dash',
                Rule::unique('services', 'slug')->ignore($this->route('service')),
            ],
            'path' => ['nullable', Rule::in(CareerPaths::keys())],
            'rate_usd' => 'nullable|integer|min:0|max:10000',
            'is_published' => 'boolean',
            ...Translations::rules('title'),
            ...Translations::rules('summary', 1000),
            'deliverables' => 'nullable|array',
            'deliverables.*' => 'nullable|array',
            'deliverables.*.*' => 'nullable|string|max:255',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function modelAttributes(): array
    {
        return [
            'slug' => $this->input('slug'),
            'path' => $this->input('path') ?: null,
            // A blank field means "no rate shown", not zero.
            'rate_usd' => filled($this->input('rate_usd')) ? (int) $this->input('rate_usd') : null,
            'is_published' => $this->boolean('is_published'),
            'title' => Translations::clean($this->input('title')),
            'summary' => Translations::clean($this->input('summary')),
            'deliverables' => $this->deliverables(),
        ];
    }

    /**
     * One list of non-blank lines per locale; a locale with none is dropped.
     *
     * @return array<string, array<int, string>>
     */
    private function deliverables(): array
    {
        $result = [];

        foreach (Translations::locales() as $locale) {
            $lines = collect($this->input("deliverables.$locale", []))
                ->map(fn ($line) => trim((string) $line))
                ->filter()
                ->values()
                ->all();

            if ($lines !== []) {
                $result[$locale] = $lines;
            }
        }

        return $result;
    }
}
