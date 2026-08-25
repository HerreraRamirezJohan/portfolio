<?php

namespace App\Livewire\Admin\Concerns;

use Livewire\Attributes\Url;

/**
 * Adds the ES|EN tab state shared by every admin form that edits
 * spatie-translatable attributes.
 */
trait HasLocaleTabs
{
    #[Url(as: 'lang', keep: true)]
    public string $formLocale = '';

    /** @return array<int, string> */
    public function getLocalesProperty(): array
    {
        return config('portfolio.locales');
    }

    public function mountHasLocaleTabs(): void
    {
        if (! in_array($this->formLocale, $this->locales, true)) {
            $this->formLocale = config('app.locale');
        }
    }

    public function setFormLocale(string $locale): void
    {
        if (in_array($locale, $this->locales, true)) {
            $this->formLocale = $locale;
        }
    }

    /**
     * A blank {locale: ''} map for a new translatable field.
     *
     * @return array<string, string>
     */
    protected function emptyTranslations(): array
    {
        return array_fill_keys($this->locales, '');
    }

    /**
     * Normalise a model's stored translations into a complete {locale: value} map,
     * so Livewire always has every locale key bound even when one is missing.
     *
     * @param  array<string, mixed>  $stored
     * @return array<string, mixed>
     */
    protected function fillTranslations(array $stored, mixed $default = ''): array
    {
        $result = [];

        foreach ($this->locales as $locale) {
            $result[$locale] = $stored[$locale] ?? $default;
        }

        return $result;
    }
}
