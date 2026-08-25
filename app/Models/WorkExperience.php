<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class WorkExperience extends Model
{
    use HasTranslations;

    protected $fillable = [
        'user_id', 'company', 'start_date', 'end_date', 'is_current',
        'sort_order', 'role', 'location', 'bullets',
    ];

    public array $translatable = ['role', 'location', 'bullets'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }

    /**
     * "Marzo 2026 - Agosto 2026", or "July 2024 - Present", in the active locale.
     */
    public function dateRange(): string
    {
        $locale = app()->getLocale();
        $start = $this->start_date->locale($locale)->isoFormat('MMMM YYYY');

        $end = $this->is_current || $this->end_date === null
            ? __('Present')
            : $this->end_date->locale($locale)->isoFormat('MMMM YYYY');

        return Str::ucfirst($start).' – '.Str::ucfirst($end);
    }
}
