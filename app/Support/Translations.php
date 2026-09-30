<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

/**
 * Moves spatie-translatable attributes across the API boundary.
 *
 * The admin API always sends and receives every translatable field as a
 * complete {locale: value} map, so the React forms can bind each ES|EN tab
 * without checking for missing keys.
 */
class Translations
{
    /**
     * @return array<int, string>
     */
    public static function locales(): array
    {
        return config('portfolio.locales');
    }

    /**
     * A model's stored translations as a map with every locale present.
     *
     * @return array<string, mixed>
     */
    public static function fill(?Model $model, string $field, mixed $default = ''): array
    {
        $stored = $model?->getTranslations($field) ?? [];

        $result = [];

        foreach (self::locales() as $locale) {
            $result[$locale] = $stored[$locale] ?? $default;
        }

        return $result;
    }

    /**
     * Drop blank locales before storing, so a missing translation falls back
     * instead of rendering as an empty string.
     *
     * @param  array<string, mixed>|null  $values
     * @return array<string, mixed>
     */
    public static function clean(?array $values): array
    {
        return array_filter(
            array_intersect_key($values ?? [], array_flip(self::locales())),
            fn ($value) => filled($value),
        );
    }

    /**
     * Save attributes onto a model, *replacing* each translatable field.
     *
     * A plain update() merges locale maps (spatie's setTranslations), so a
     * translation blanked in the admin would silently survive. The admin
     * always sends every locale, so a replace is the correct semantics.
     *
     * @template TModel of Model
     *
     * @param  TModel  $model
     * @param  array<string, mixed>  $attributes
     * @return TModel
     */
    public static function update(Model $model, array $attributes): Model
    {
        foreach ($attributes as $key => $value) {
            if (is_array($value) && $model->isTranslatableAttribute($key)) {
                $model->replaceTranslations($key, $value);
            } else {
                $model->setAttribute($key, $value);
            }
        }

        $model->save();

        return $model;
    }

    /**
     * Validation rules for a {locale: string} map.
     *
     * @return array<string, string>
     */
    public static function rules(string $field, int $max = 255): array
    {
        return [
            $field => 'nullable|array',
            $field.'.*' => 'nullable|string|max:'.$max,
        ];
    }
}
