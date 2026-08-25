<div>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-xl font-semibold text-stone-900 dark:text-stone-50">{{ __('Profile') }}</h1>
        <x-admin.locale-tabs :locales="$this->locales" :current="$formLocale" />
    </div>

    <x-admin.status />

    <form class="space-y-5" wire:submit="save">
        <x-admin.field :label="__('Name')" for="name">
            <input class="w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm dark:border-stone-700 dark:bg-stone-900"
                   id="name" type="text" wire:model="name">
            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </x-admin.field>

        @foreach (['headline' => __('Headline'), 'location' => __('Location')] as $field => $label)
            <x-admin.field :label="$label . ' (' . strtoupper($formLocale) . ')'">
                <input class="w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm dark:border-stone-700 dark:bg-stone-900"
                       type="text" wire:model="{{ $field }}.{{ $formLocale }}" wire:key="{{ $field }}-{{ $formLocale }}">
            </x-admin.field>
        @endforeach

        <x-admin.field :label="__('Profile') . ' (' . strtoupper($formLocale) . ')'">
            <textarea class="w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm dark:border-stone-700 dark:bg-stone-900"
                      rows="7" wire:model="summary.{{ $formLocale }}" wire:key="summary-{{ $formLocale }}"></textarea>
        </x-admin.field>

        <div class="grid gap-5 sm:grid-cols-2">
            @foreach ([
                'phone' => __('Phone'),
                'public_email' => __('Email'),
                'linkedin_url' => 'LinkedIn URL',
                'github_url' => 'GitHub URL',
                'website_url' => __('Website'),
            ] as $field => $label)
                <x-admin.field :label="$label">
                    <input class="w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm dark:border-stone-700 dark:bg-stone-900"
                           type="text" wire:model="{{ $field }}">
                    @error($field) <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </x-admin.field>
            @endforeach
        </div>

        <button class="rounded-md bg-stone-900 px-4 py-2 text-sm font-medium text-stone-50 hover:bg-stone-800 dark:bg-stone-100 dark:text-stone-900"
                type="submit">{{ __('Save') }}</button>
    </form>
</div>
