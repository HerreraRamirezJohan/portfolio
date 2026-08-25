@props(['locales', 'current'])
<div class="inline-flex rounded-md border border-stone-300 p-0.5 dark:border-stone-700">
    @foreach ($locales as $locale)
        <button type="button" wire:click="setFormLocale('{{ $locale }}')"
                @class([
                    'rounded px-2.5 py-1 text-xs font-medium transition',
                    'bg-stone-900 text-stone-50 dark:bg-stone-100 dark:text-stone-900' => $locale === $current,
                    'text-stone-500 hover:text-stone-900 dark:text-stone-400 dark:hover:text-stone-100' => $locale !== $current,
                ])>{{ strtoupper($locale) }}</button>
    @endforeach
</div>
