@props(['title', 'locales', 'current'])
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-xl font-semibold text-stone-900 dark:text-stone-50">{{ $title }}</h1>
    <div class="flex items-center gap-3">
        <x-admin.locale-tabs :locales="$locales" :current="$current" />
        <button class="rounded-md bg-stone-900 px-3 py-1.5 text-sm font-medium text-stone-50 hover:bg-stone-800 dark:bg-stone-100 dark:text-stone-900"
                type="button" wire:click="create">{{ __('Add') }}</button>
    </div>
</div>
