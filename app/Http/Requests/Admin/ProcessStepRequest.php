<?php

namespace App\Http\Requests\Admin;

use App\Support\Translations;
use Illuminate\Foundation\Http\FormRequest;

class ProcessStepRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            ...Translations::rules('title'),
            ...Translations::rules('body', 1000),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function modelAttributes(): array
    {
        return [
            'title' => Translations::clean($this->input('title')),
            'body' => Translations::clean($this->input('body')),
        ];
    }
}
