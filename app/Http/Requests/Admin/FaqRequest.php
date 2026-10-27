<?php

namespace App\Http\Requests\Admin;

use App\Support\Translations;
use Illuminate\Foundation\Http\FormRequest;

class FaqRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            ...Translations::rules('question'),
            ...Translations::rules('answer', 2000),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function modelAttributes(): array
    {
        return [
            'question' => Translations::clean($this->input('question')),
            'answer' => Translations::clean($this->input('answer')),
        ];
    }
}
