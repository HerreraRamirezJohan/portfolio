<?php

namespace App\Http\Resources\Admin;

use App\Models\User;
use App\Support\Translations;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin User
 */
class ProfileResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $profile = $this->profile;

        return [
            'name' => $this->name,
            'email' => $this->email,
            'photo_url' => $profile?->photo_path ? Storage::url($profile->photo_path) : null,
            'phone' => $profile?->phone ?? '',
            'public_email' => $profile?->public_email ?? '',
            'linkedin_url' => $profile?->linkedin_url ?? '',
            'github_url' => $profile?->github_url ?? '',
            'website_url' => $profile?->website_url ?? '',
            'headline' => Translations::fill($profile, 'headline'),
            'location' => Translations::fill($profile, 'location'),
            'summary' => Translations::fill($profile, 'summary'),
        ];
    }
}
