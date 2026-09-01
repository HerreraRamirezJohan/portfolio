@php
    // Drawn from the real skill list rather than a hand-written array, so the
    // strip stays in step with whatever is in the admin.
    $marks = $user->skillGroups
        ->flatMap->skills
        ->pluck('name')
        ->filter(fn ($name) => \App\Support\TechIcon::slug($name) !== null)
        ->unique(fn ($name) => \App\Support\TechIcon::slug($name))
        ->values();
@endphp

@if ($marks->isNotEmpty())
    <section class="overflow-hidden border-y border-line bg-elevated py-6" aria-hidden="true">
        {{-- Track is rendered twice; the keyframes translate exactly -50% so
             the seam is invisible. --}}
        <div class="marquee-track flex w-max items-center gap-14 text-steel">
            @foreach ([1, 2] as $pass)
                @foreach ($marks as $name)
                    <span class="flex shrink-0 items-center gap-2.5 opacity-70">
                        <x-ui.tech-icon :name="$name" :size="26" />
                        <span class="font-mono text-[13px] whitespace-nowrap">{{ $name }}</span>
                    </span>
                @endforeach
            @endforeach
        </div>
    </section>
@endif
