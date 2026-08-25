@props(['id'])
<div class="flex shrink-0 items-center gap-3 text-sm">
    <button class="text-stone-500 hover:text-stone-900 dark:hover:text-stone-100" type="button"
            title="{{ __('Move up') }}" wire:click="moveUp({{ $id }})">↑</button>
    <button class="text-stone-500 hover:text-stone-900 dark:hover:text-stone-100" type="button"
            title="{{ __('Move down') }}" wire:click="moveDown({{ $id }})">↓</button>
    <button class="text-stone-600 underline underline-offset-2 hover:text-stone-900 dark:text-stone-400"
            type="button" wire:click="edit({{ $id }})">{{ __('Edit') }}</button>
    <button class="text-red-600 hover:underline dark:text-red-400" type="button"
            wire:click="delete({{ $id }})" wire:confirm="{{ __('Are you sure?') }}">{{ __('Delete') }}</button>
</div>
