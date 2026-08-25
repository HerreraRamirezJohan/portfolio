<div>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-xl font-semibold text-stone-900 dark:text-stone-50">{{ __('Experience') }}</h1>
        <div class="flex items-center gap-3">
            <x-admin.locale-tabs :locales="$this->locales" :current="$formLocale" />
            <button class="rounded-md bg-stone-900 px-3 py-1.5 text-sm font-medium text-stone-50 hover:bg-stone-800 dark:bg-stone-100 dark:text-stone-900"
                    type="button" wire:click="create">{{ __('Add') }}</button>
        </div>
    </div>

    <x-admin.status />

    {{-- Editor --}}
    @if ($editingId !== null)
        <form class="mb-8 space-y-5 rounded-lg border border-stone-300 bg-white p-5 dark:border-stone-700 dark:bg-stone-900" wire:submit="save">
            <div class="grid gap-5 sm:grid-cols-2">
                <x-admin.field :label="__('Company')">
                    <input class="w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm dark:border-stone-700 dark:bg-stone-950" type="text" wire:model="company">
                    @error('company') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </x-admin.field>

                <x-admin.field :label="__('Location') . ' (' . strtoupper($formLocale) . ')'">
                    <input class="w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm dark:border-stone-700 dark:bg-stone-950"
                           type="text" wire:model="location.{{ $formLocale }}" wire:key="loc-{{ $formLocale }}">
                </x-admin.field>

                <x-admin.field :label="__('Role') . ' (' . strtoupper($formLocale) . ')'">
                    <input class="w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm dark:border-stone-700 dark:bg-stone-950"
                           type="text" wire:model="role.{{ $formLocale }}" wire:key="role-{{ $formLocale }}">
                </x-admin.field>

                <div class="grid grid-cols-2 gap-3">
                    <x-admin.field :label="__('Start')">
                        <input class="w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm dark:border-stone-700 dark:bg-stone-950" type="date" wire:model="start_date">
                        @error('start_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </x-admin.field>

                    <x-admin.field :label="__('End')">
                        <input class="w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm disabled:opacity-40 dark:border-stone-700 dark:bg-stone-950"
                               type="date" wire:model="end_date" @disabled($is_current)>
                        @error('end_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </x-admin.field>
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input class="rounded border-stone-300 dark:border-stone-700" type="checkbox" wire:model.live="is_current">
                {{ __('Current role') }}
            </label>

            {{-- Bullets, per locale --}}
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-stone-700 dark:text-stone-300">
                        {{ __('Bullets') }} ({{ strtoupper($formLocale) }})
                    </span>
                    <button class="text-sm text-stone-600 underline underline-offset-2 hover:text-stone-900 dark:text-stone-400"
                            type="button" wire:click="addBullet">{{ __('Add') }}</button>
                </div>

                <div class="mt-2 space-y-3">
                    @forelse ($bullets[$formLocale] ?? [] as $index => $bullet)
                        <div class="rounded-md border border-stone-200 p-3 dark:border-stone-800" wire:key="bullet-{{ $formLocale }}-{{ $index }}">
                            <input class="w-full rounded-md border border-stone-300 bg-white px-3 py-1.5 text-sm dark:border-stone-700 dark:bg-stone-950"
                                   type="text" placeholder="{{ __('Label (optional)') }}" wire:model="bullets.{{ $formLocale }}.{{ $index }}.label">
                            <textarea class="mt-2 w-full rounded-md border border-stone-300 bg-white px-3 py-1.5 text-sm dark:border-stone-700 dark:bg-stone-950"
                                      rows="3" placeholder="{{ __('Body') }}" wire:model="bullets.{{ $formLocale }}.{{ $index }}.body"></textarea>
                            <button class="mt-1 text-xs text-red-600 hover:underline dark:text-red-400"
                                    type="button" wire:click="removeBullet({{ $index }})">{{ __('Delete') }}</button>
                        </div>
                    @empty
                        <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('No items yet.') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="flex gap-3">
                <button class="rounded-md bg-stone-900 px-4 py-2 text-sm font-medium text-stone-50 hover:bg-stone-800 dark:bg-stone-100 dark:text-stone-900"
                        type="submit">{{ __('Save') }}</button>
                <button class="rounded-md border border-stone-300 px-4 py-2 text-sm dark:border-stone-700"
                        type="button" wire:click="cancel">{{ __('Cancel') }}</button>
            </div>
        </form>
    @endif

    {{-- List --}}
    <div class="space-y-3">
        @forelse ($jobs as $job)
            <div class="flex items-start justify-between gap-4 rounded-lg border border-stone-200 bg-white p-4 dark:border-stone-800 dark:bg-stone-900"
                 wire:key="job-{{ $job->id }}">
                <div>
                    <p class="font-medium text-stone-900 dark:text-stone-50">{{ $job->company }}</p>
                    <p class="text-sm text-stone-600 dark:text-stone-400">
                        {{ $job->getTranslation('role', $formLocale, false) ?: '—' }}
                    </p>
                    <p class="mt-0.5 text-xs text-stone-500 dark:text-stone-500">{{ $job->dateRange() }}</p>
                </div>
                <div class="flex shrink-0 items-center gap-3 text-sm">
                    <button class="text-stone-500 hover:text-stone-900 dark:hover:text-stone-100" type="button"
                            title="{{ __('Move up') }}" wire:click="moveUp({{ $job->id }})">↑</button>
                    <button class="text-stone-500 hover:text-stone-900 dark:hover:text-stone-100" type="button"
                            title="{{ __('Move down') }}" wire:click="moveDown({{ $job->id }})">↓</button>
                    <button class="text-stone-600 underline underline-offset-2 hover:text-stone-900 dark:text-stone-400"
                            type="button" wire:click="edit({{ $job->id }})">{{ __('Edit') }}</button>
                    <button class="text-red-600 hover:underline dark:text-red-400" type="button"
                            wire:click="delete({{ $job->id }})"
                            wire:confirm="{{ __('Are you sure?') }}">{{ __('Delete') }}</button>
                </div>
            </div>
        @empty
            <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('No items yet.') }}</p>
        @endforelse
    </div>
</div>
