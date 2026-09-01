@props(['eyebrow' => null, 'title' => null])

<div {{ $attributes->merge(['class' => 'reveal']) }}>
    @if ($eyebrow)
        <p class="mb-4 font-mono text-[11.5px] uppercase tracking-[0.16em] text-accent">{{ $eyebrow }}</p>
    @endif

    @if ($title)
        <h2 class="font-display text-[clamp(2.125rem,4.5vw,3.375rem)] font-normal leading-[1.05] tracking-[-0.015em] text-pretty">
            {{ $title }}
        </h2>
    @endif

    {{ $slot }}
</div>
