<?php

namespace App\Http\Requests\Admin;

use App\Support\Translations;
use Illuminate\Foundation\Http\FormRequest;

class ExperienceRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'company' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'boolean',
            ...Translations::rules('role'),
            ...Translations::rules('location'),
            'bullets' => 'nullable|array',
            'bullets.*' => 'nullable|array',
            'bullets.*.*.label' => 'nullable|string|max:255',
            'bullets.*.*.body' => 'nullable|string|max:2000',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function modelAttributes(): array
    {
        $current = $this->boolean('is_current');

        return [
            'company' => $this->input('company'),
            'start_date' => $this->input('start_date'),
            'end_date' => $current ? null : ($this->input('end_date') ?: null),
            'is_current' => $current,
            'role' => Translations::clean($this->input('role')),
            'location' => Translations::clean($this->input('location')),
            'bullets' => $this->bullets(),
        ];
    }

    /**
     * Drop bullets with no body, and the label when it is blank, so the
     * public side's "has a label" check stays meaningful.
     *
     * @return array<string, array<int, array{label: string|null, body: string}>>
     */
    private function bullets(): array
    {
        $result = [];

        foreach (Translations::locales() as $locale) {
            $cleaned = collect($this->input("bullets.$locale", []))
                ->filter(fn ($row) => is_array($row) && filled($row['body'] ?? null))
                ->map(fn (array $row) => [
                    'label' => filled($row['label'] ?? null) ? $row['label'] : null,
                    'body' => $row['body'],
                ])
                ->values()
                ->all();

            if ($cleaned !== []) {
                $result[$locale] = $cleaned;
            }
        }

        return $result;
    }
}
