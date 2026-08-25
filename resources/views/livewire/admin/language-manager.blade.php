<div>
    <x-admin.page-header :title="__('Languages')" :locales="$this->locales" :current="$formLocale" />
    <x-admin.status />

    @if ($editingId !== null)
        <form class="mb-8 space-y-5 rounded-lg border border-stone-300 bg-white p-5 dark:border-stone-700 dark:bg-stone-900" wire:submit="save">
            <div class="grid gap-5 sm:grid-cols-3">
                <x-admin.field :label="__('Code')">
                    <x-admin.text-input wire:model="code" placeholder="es" />
                    @error('code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </x-admin.field>

                <x-admin.field :label="__('Name') . ' (' . strtoupper($formLocale) . ')'">
                    <x-admin.text-input wire:model="name.{{ $formLocale }}" wire:key="lang-name-{{ $formLocale }}" />
                </x-admin.field>

                <x-admin.field :label="__('Level') . ' (' . strtoupper($formLocale) . ')'">
                    <x-admin.text-input wire:model="level_label.{{ $formLocale }}" wire:key="lang-lvl-{{ $formLocale }}" />
                </x-admin.field>
            </div>

            <x-admin.buttons />
        </form>
    @endif

    <div class="space-y-3">
        @forelse ($rows as $row)
            <div class="flex items-center justify-between gap-4 rounded-lg border border-stone-200 bg-white p-4 dark:border-stone-800 dark:bg-stone-900"
                 wire:key="lang-{{ $row->id }}">
                <div>
                    <p class="font-medium text-stone-900 dark:text-stone-50">{{ $row->getTranslation('name', $formLocale, false) ?: $row->code }}</p>
                    <p class="text-sm text-stone-600 dark:text-stone-400">{{ $row->getTranslation('level_label', $formLocale, false) ?: '—' }}</p>
                </div>
                <x-admin.row-actions :id="$row->id" />
            </div>
        @empty
            <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('No items yet.') }}</p>
        @endforelse
    </div>
</div>
