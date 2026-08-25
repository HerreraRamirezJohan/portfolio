<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Education extends Model
{
    use HasTranslations;

    /**
     * The inflector treats "education" as uncountable, so it would resolve to
     * `education` rather than the `educations` table the migration creates.
     */
    protected $table = 'educations';

    protected $fillable = [
        'user_id', 'institution', 'start_year', 'end_year',
        'sort_order', 'degree', 'location', 'notes',
    ];

    public array $translatable = ['degree', 'location', 'notes'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }
}
