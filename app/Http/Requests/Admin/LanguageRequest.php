<?php

namespace App\Http\Requests\Admin;

use App\Support\Translations;
use Illuminate\Foundation\Http\FormRequest;

class LanguageRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'code' => 'required|string|max:5',
            ...Translations::rules('name'),
            ...Translations::rules('level_label'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function modelAttributes(): array
    {
        return [
            'code' => $this->input('code'),
            'name' => Translations::clean($this->input('name')),
            'level_label' => Translations::clean($this->input('level_label')),
        ];
    }
}
