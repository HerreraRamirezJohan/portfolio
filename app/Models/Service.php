<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

/**
 * Something the owner sells as a freelancer: what it is, the topics it
 * covers and an hourly rate in US dollars. Shown on the home page; `path`
 * names the career path whose page holds the supporting work.
 */
class Service extends Model
{
    use HasTranslations;

    protected $fillable = [
        'user_id', 'slug', 'path', 'rate_usd', 'is_published', 'sort_order',
        'title', 'summary', 'deliverables',
    ];

    public array $translatable = ['title', 'summary', 'deliverables'];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
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
