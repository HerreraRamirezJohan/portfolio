<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * The site owner as the public pages show them: nav, hero and contact band.
 *
 * @mixin User
 */
class PersonResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $profile = $this->profile;

        return [
            'name' => $this->name,
            'short_name' => $this->shortName(),
            'email' => $profile?->public_email ?? $this->email,
            'headline' => $profile?->headline ?: null,
            'location' => $profile?->location ?: null,
            'photo_url' => $profile?->photo_path ? Storage::url($profile->photo_path) : null,
            'phone' => $profile?->phone,
            'phone_href' => $profile?->phone ? 'tel:'.preg_replace('/[^\d+]/', '', $profile->phone) : null,
            'github_url' => $profile?->github_url,
            'linkedin_url' => $profile?->linkedin_url,
            'website_url' => $profile?->website_url,
        ];
    }
}
