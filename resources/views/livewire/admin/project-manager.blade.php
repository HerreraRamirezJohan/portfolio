<div>
    <x-admin.page-header :title="__('Projects')" :locales="$this->locales" :current="$formLocale" />
    <x-admin.status />

    @if ($editingId !== null)
        <form class="mb-8 space-y-5 rounded-lg border border-stone-300 bg-white p-5 dark:border-stone-700 dark:bg-stone-900" wire:submit="save">
            <x-admin.field :label="__('Title') . ' (' . strtoupper($formLocale) . ')'">
                <x-admin.text-input wire:model.blur="title.{{ $formLocale }}" wire:key="proj-title-{{ $formLocale }}" />
            </x-admin.field>

            <div class="grid gap-5 sm:grid-cols-3">
                <x-admin.field :label="__('Slug')">
                    <x-admin.text-input wire:model="slug" />
                    @error('slug') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </x-admin.field>
                <x-admin.field label="Repository URL">
                    <x-admin.text-input wire:model="repo_url" />
                    @error('repo_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </x-admin.field>
                <x-admin.field label="Live URL">
                    <x-admin.text-input wire:model="live_url" />
                    @error('live_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </x-admin.field>
            </div>

            <div class="grid gap-5 sm:grid-cols-3">
                <x-admin.field :label="__('Year')">
                    <x-admin.text-input wire:model="year" placeholder="2024 – 2026" />
                    @error('year') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </x-admin.field>
                <div class="sm:col-span-2">
                    <x-admin.field :label="__('Tech stack')">
                        <x-admin.text-input wire:model="tech_stack_csv" placeholder="Laravel, PostgreSQL, Docker" />
                        <p class="mt-1 text-xs text-stone-500 dark:text-stone-400">{{ __('Comma separated. Names matching the icon set render with their mark.') }}</p>
                        @error('tech_stack_csv') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </x-admin.field>
                </div>
            </div>

            <x-admin.field :label="__('Summary') . ' (' . strtoupper($formLocale) . ')'">
                <textarea class="w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm dark:border-stone-700 dark:bg-stone-950"
                          rows="2" wire:model="summary.{{ $formLocale }}" wire:key="proj-sum-{{ $formLocale }}"></textarea>
            </x-admin.field>

            <x-admin.field :label="__('Description') . ' (' . strtoupper($formLocale) . ')'">
                <textarea class="w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm dark:border-stone-700 dark:bg-stone-950"
                          rows="5" wire:model="description.{{ $formLocale }}" wire:key="proj-desc-{{ $formLocale }}"></textarea>
            </x-admin.field>

            <label class="flex items-center gap-2 text-sm">
                <input class="rounded border-stone-300 dark:border-stone-700" type="checkbox" wire:model="is_published">
                {{ __('Published') }}
            </label>

            <x-admin.buttons />
        </form>
    @endif

    <div class="space-y-3">
        @forelse ($rows as $row)
            <div class="flex items-start justify-between gap-4 rounded-lg border border-stone-200 bg-white p-4 dark:border-stone-800 dark:bg-stone-900"
                 wire:key="proj-{{ $row->id }}">
                <div>
                    <p class="font-medium text-stone-900 dark:text-stone-50">
                        {{ $row->getTranslation('title', $formLocale, false) ?: $row->slug }}
                        @unless ($row->is_published)
                            <span class="ml-1 rounded bg-stone-200 px-1.5 py-0.5 text-xs text-stone-600 dark:bg-stone-800 dark:text-stone-400">{{ __('Draft') }}</span>
                        @endunless
                    </p>
                    <p class="text-sm text-stone-600 dark:text-stone-400">{{ $row->getTranslation('summary', $formLocale, false) ?: '—' }}</p>
                </div>
                <x-admin.row-actions :id="$row->id" />
            </div>
        @empty
            <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('No items yet.') }}</p>
        @endforelse
    </div>
</div>
