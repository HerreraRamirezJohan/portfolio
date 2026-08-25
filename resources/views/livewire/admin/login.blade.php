<div class="mx-auto max-w-sm">
    <h1 class="text-xl font-semibold text-stone-900 dark:text-stone-50">{{ __('Log in') }}</h1>

    <form class="mt-6 space-y-4" wire:submit="authenticate">
        <div>
            <label class="block text-sm font-medium" for="email">{{ __('Email') }}</label>
            <input class="mt-1 w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm dark:border-stone-700 dark:bg-stone-900"
                   id="email" type="email" autocomplete="username" required wire:model="email">
            @error('email') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium" for="password">{{ __('Password') }}</label>
            <input class="mt-1 w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm dark:border-stone-700 dark:bg-stone-900"
                   id="password" type="password" autocomplete="current-password" required wire:model="password">
            @error('password') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-2 text-sm">
            <input class="rounded border-stone-300 dark:border-stone-700" type="checkbox" wire:model="remember">
            {{ __('Remember me') }}
        </label>

        <button class="w-full rounded-md bg-stone-900 px-4 py-2 text-sm font-medium text-stone-50 hover:bg-stone-800 dark:bg-stone-100 dark:text-stone-900 dark:hover:bg-stone-200"
                type="submit">{{ __('Log in') }}</button>
    </form>
</div>
