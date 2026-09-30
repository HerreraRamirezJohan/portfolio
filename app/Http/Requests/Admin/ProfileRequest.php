<?php

namespace App\Http\Requests\Admin;

use App\Support\Translations;
use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'public_email' => 'nullable|email|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'github_url' => 'nullable|url|max:255',
            'website_url' => 'nullable|url|max:255',
            ...Translations::rules('headline'),
            ...Translations::rules('location'),
            ...Translations::rules('summary', 5000),
        ];
    }

    /**
     * Profile row attributes (the name lives on the user).
     *
     * @return array<string, mixed>
     */
    public function profileAttributes(): array
    {
        return [
            'phone' => $this->input('phone') ?: null,
            'public_email' => $this->input('public_email') ?: null,
            'linkedin_url' => $this->input('linkedin_url') ?: null,
            'github_url' => $this->input('github_url') ?: null,
            'website_url' => $this->input('website_url') ?: null,
            'headline' => Translations::clean($this->input('headline')),
            'location' => Translations::clean($this->input('location')),
            'summary' => Translations::clean($this->input('summary')),
        ];
    }
}
