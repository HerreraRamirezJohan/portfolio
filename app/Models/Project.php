<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Project extends Model
{
    use HasTranslations;

    protected $fillable = [
        'user_id', 'slug', 'repo_url', 'live_url', 'image_path',
        'tech_stack', 'year', 'is_published', 'sort_order',
        'title', 'summary', 'description',
    ];

    public array $translatable = ['title', 'summary', 'description'];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'tech_stack' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }
}
