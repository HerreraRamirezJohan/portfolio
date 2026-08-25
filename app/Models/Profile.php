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

    public array $translatable = ['headline', 'location', 'summary'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
