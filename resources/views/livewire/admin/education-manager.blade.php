<div>
    <x-admin.page-header :title="__('Education')" :locales="$this->locales" :current="$formLocale" />
    <x-admin.status />

    @if ($editingId !== null)
        <form class="mb-8 space-y-5 rounded-lg border border-stone-300 bg-white p-5 dark:border-stone-700 dark:bg-stone-900" wire:submit="save">
            <div class="grid gap-5 sm:grid-cols-2">
                <x-admin.field :label="__('Institution')">
                    <x-admin.text-input wire:model="institution" />
                    @error('institution') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </x-admin.field>

                <x-admin.field :label="__('Location') . ' (' . strtoupper($formLocale) . ')'">
                    <x-admin.text-input wire:model="location.{{ $formLocale }}" wire:key="edu-loc-{{ $formLocale }}" />
                </x-admin.field>

                <x-admin.field :label="__('Degree') . ' (' . strtoupper($formLocale) . ')'">
                    <x-admin.text-input wire:model="degree.{{ $formLocale }}" wire:key="edu-deg-{{ $formLocale }}" />
                </x-admin.field>

                <div class="grid grid-cols-2 gap-3">
                    <x-admin.field :label="__('Start')">
                        <x-admin.text-input type="number" wire:model="start_year" />
                        @error('start_year') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </x-admin.field>
                    <x-admin.field :label="__('End')">
                        <x-admin.text-input type="number" wire:model="end_year" />
                        @error('end_year') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </x-admin.field>
                </div>
            </div>

            <x-admin.field :label="__('Notes') . ' (' . strtoupper($formLocale) . ')'">
                <textarea class="w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm dark:border-stone-700 dark:bg-stone-950"
                          rows="3" wire:model="notes.{{ $formLocale }}" wire:key="edu-notes-{{ $formLocale }}"></textarea>
            </x-admin.field>

            <x-admin.buttons />
        </form>
    @endif

    <div class="space-y-3">
        @forelse ($rows as $row)
            <div class="flex items-start justify-between gap-4 rounded-lg border border-stone-200 bg-white p-4 dark:border-stone-800 dark:bg-stone-900"
                 wire:key="edu-{{ $row->id }}">
                <div>
                    <p class="font-medium text-stone-900 dark:text-stone-50">{{ $row->institution }}</p>
                    <p class="text-sm text-stone-600 dark:text-stone-400">{{ $row->getTranslation('degree', $formLocale, false) ?: '—' }}</p>
                    <p class="mt-0.5 text-xs text-stone-500">{{ collect([$row->start_year, $row->end_year])->filter()->implode(' – ') }}</p>
                </div>
                <x-admin.row-actions :id="$row->id" />
            </div>
        @empty
            <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('No items yet.') }}</p>
        @endforelse
    </div>
</div>
