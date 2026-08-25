<div>
    <x-admin.page-header :title="__('Technical Skills')" :locales="$this->locales" :current="$formLocale" />
    <x-admin.status />

    @if ($editingId !== null)
        <form class="mb-8 space-y-5 rounded-lg border border-stone-300 bg-white p-5 dark:border-stone-700 dark:bg-stone-900" wire:submit="save">
            <x-admin.field :label="__('Label') . ' (' . strtoupper($formLocale) . ')'">
                <x-admin.text-input wire:model="label.{{ $formLocale }}" wire:key="sg-label-{{ $formLocale }}" />
            </x-admin.field>

            <x-admin.field :label="__('Description') . ' (' . strtoupper($formLocale) . ')'">
                <textarea class="w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm dark:border-stone-700 dark:bg-stone-950"
                          rows="3" wire:model="description.{{ $formLocale }}" wire:key="sg-desc-{{ $formLocale }}"></textarea>
            </x-admin.field>

            <div>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-stone-700 dark:text-stone-300">{{ __('Skills') }}</span>
                    <button class="text-sm text-stone-600 underline underline-offset-2 hover:text-stone-900 dark:text-stone-400"
                            type="button" wire:click="addSkill">{{ __('Add') }}</button>
                </div>
                <p class="mt-1 text-xs text-stone-500 dark:text-stone-500">{{ __('Not translated — proper nouns like PHP, React, Docker.') }}</p>

                <div class="mt-2 grid gap-2 sm:grid-cols-2">
                    @forelse ($skillNames as $index => $skill)
                        <div class="flex items-center gap-2" wire:key="skill-{{ $index }}">
                            <x-admin.text-input wire:model="skillNames.{{ $index }}" />
                            <button class="shrink-0 text-xs text-red-600 hover:underline dark:text-red-400"
                                    type="button" wire:click="removeSkill({{ $index }})">✕</button>
                        </div>
                    @empty
                        <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('No items yet.') }}</p>
                    @endforelse
                </div>
            </div>

            <x-admin.buttons />
        </form>
    @endif

    <div class="space-y-3">
        @forelse ($rows as $row)
            <div class="flex items-start justify-between gap-4 rounded-lg border border-stone-200 bg-white p-4 dark:border-stone-800 dark:bg-stone-900"
                 wire:key="sg-{{ $row->id }}">
                <div>
                    <p class="font-medium text-stone-900 dark:text-stone-50">{{ $row->getTranslation('label', $formLocale, false) ?: '—' }}</p>
                    <p class="mt-1 text-sm text-stone-600 dark:text-stone-400">{{ $row->skills->pluck('name')->implode(', ') ?: '—' }}</p>
                </div>
                <x-admin.row-actions :id="$row->id" />
            </div>
        @empty
            <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('No items yet.') }}</p>
        @endforelse
    </div>
</div>
