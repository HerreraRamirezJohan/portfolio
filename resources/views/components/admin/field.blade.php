@props(['label', 'for' => null])
<div>
    <label class="block text-sm font-medium text-stone-700 dark:text-stone-300" @if ($for) for="{{ $for }}" @endif>
        {{ $label }}
    </label>
    <div class="mt-1">{{ $slot }}</div>
</div>
