<?php

namespace App\Http\Requests\Admin;

use App\Support\Translations;
use Illuminate\Foundation\Http\FormRequest;

class EducationRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'institution' => 'required|string|max:255',
            'start_year' => 'nullable|integer|min:1900|max:2100',
            'end_year' => 'nullable|integer|min:1900|max:2100|gte:start_year',
            ...Translations::rules('degree'),
            ...Translations::rules('location'),
            ...Translations::rules('notes', 2000),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function modelAttributes(): array
    {
        return [
            'institution' => $this->input('institution'),
            'start_year' => $this->input('start_year'),
            'end_year' => $this->input('end_year'),
            'degree' => Translations::clean($this->input('degree')),
            'location' => Translations::clean($this->input('location')),
            'notes' => Translations::clean($this->input('notes')),
        ];
    }
}
