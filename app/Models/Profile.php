<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Profile extends Model
{
    use HasTranslations;

    protected $fillable = [
        'user_id', 'phone', 'public_email', 'linkedin_url', 'github_url',
        'website_url', 'photo_path', 'headline', 'location', 'summary',
    ];

    /**
     * The photo seeded from storage/app/public/profile. It is not tracked in
     * git, so it is the only copy and must never be deleted by the admin.
     */
    public const DEFAULT_PHOTO = 'profile/profile.webp';

    public array $translatable = ['headline', 'location', 'summary'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
