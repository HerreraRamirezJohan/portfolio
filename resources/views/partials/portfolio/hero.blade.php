@php
    // The name is split so the surname can carry the accent colour, matching
    // the approved design. Falls back gracefully for a single-word name.
    $parts = preg_split('/\s+/', trim($user->name));
    $given = implode(' ', array_slice($parts, 0, 2));
    $family = implode(' ', array_slice($parts, 2)) ?: null;

    $socials = array_filter([
        'GitHub' => $profile?->github_url,
        'LinkedIn' => $profile?->linkedin_url,
        'Website' => $profile?->website_url,
    ]);
@endphp

<section id="home" class="relative overflow-hidden px-5 pb-20 pt-28 sm:px-8 sm:pb-24 sm:pt-32 lg:px-12 lg:pb-28 lg:pt-36">
    {{-- Ambient grid, masked to a soft ellipse. Decorative only. --}}
    <div aria-hidden="true"
         class="pointer-events-none absolute inset-0 opacity-[0.55]
                [background-image:linear-gradient(var(--c-line-soft)_1px,transparent_1px),linear-gradient(90deg,var(--c-line-soft)_1px,transparent_1px)]
                [background-size:64px_64px]
                [mask-image:radial-gradient(ellipse_80%_60%_at_50%_40%,#000_40%,transparent_100%)]"></div>
    <div aria-hidden="true"
         class="pointer-events-none absolute -top-32 right-0 size-[620px] max-w-full rounded-full
                bg-[radial-gradient(circle,var(--c-accent-soft)_0%,transparent_70%)]"></div>

    <div class="relative mx-auto grid max-w-[1400px] items-center gap-14 lg:grid-cols-[1.15fr_0.85fr] lg:gap-18"
         data-reveal-group>

        <div class="flex flex-col gap-7">
            <div class="reveal flex flex-wrap items-center gap-3">
                <span class="flex items-center gap-2 rounded-full border border-accent-line bg-accent-soft px-3.5 py-1.5 font-mono text-[11.5px] uppercase tracking-[0.14em] text-accent">
                    <span class="size-1.5 rounded-full bg-accent"></span>
                    {{ __('Open to opportunities') }}
                </span>
                @if ($profile?->location)
                    <span class="font-mono text-[11.5px] uppercase tracking-[0.14em] text-faint">
                        {{ $profile->location }}
                    </span>
                @endif
            </div>

            <h1 class="reveal font-display text-[clamp(2.75rem,7.5vw,5.75rem)] font-normal leading-[0.95] tracking-[-0.02em] text-pretty">
                {{ $given }}
                @if ($family)
                    <br><span class="italic text-accent">{{ $family }}</span>
                @endif
            </h1>

            @if ($profile?->headline)
                <div class="reveal flex flex-wrap items-center gap-x-3.5 gap-y-1">
                    <span aria-hidden="true" class="hidden h-px w-12 bg-accent sm:block"></span>
                    <p class="text-lg font-medium">{{ $profile->headline }}</p>
                    {{-- The separator only reads correctly when both parts sit
                         on one line; on narrow screens they stack. --}}
                    <span aria-hidden="true" class="hidden text-faint sm:inline">·</span>
                    <p class="text-lg text-muted">{{ __('Full-stack & SAP enterprise systems') }}</p>
                </div>
            @endif

            @if ($profile?->summary)
                <p class="reveal max-w-xl text-[16.5px] leading-[1.72] text-muted text-pretty">
                    {{ \Illuminate\Support\Str::of($profile->summary)->explode('. ')->slice(-2)->implode('. ') }}
                </p>
            @endif

            <div class="reveal mt-1 flex flex-wrap items-center gap-3.5">
                <a href="#experience"
                   class="flex h-[52px] items-center gap-2.5 rounded-[10px] bg-accent px-6 text-[15px] font-semibold text-accent-ink transition-opacity hover:opacity-90">
                    {{ __('View my work') }}
                    <svg class="size-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 12h14M13 6l6 6-6 6"/>
                    </svg>
                </a>
                @if ($profile?->public_email)
                    <a href="mailto:{{ $profile->public_email }}"
                       class="flex h-[52px] items-center gap-2.5 rounded-[10px] border border-line px-6 text-[15px] font-medium transition-colors hover:border-accent-line">
                        <svg class="size-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="2.5" y="4.5" width="19" height="15" rx="2.5"/><path d="m3 6.5 9 6 9-6"/>
                        </svg>
                        {{ __('Get in touch') }}
                    </a>
                @endif
            </div>

            @if ($socials)
                <div class="reveal flex flex-wrap items-center gap-x-5 gap-y-2 font-mono text-[12.5px] text-faint">
                    @foreach ($socials as $label => $url)
                        @if (! $loop->first)
                            <span aria-hidden="true" class="opacity-40">/</span>
                        @endif
                        <a href="{{ $url }}" rel="noopener noreferrer" target="_blank"
                           class="transition-colors hover:text-accent">{{ $label }}</a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Portrait. The source photo is 200x200, so the frame stays small
             deliberately -- enlarging it would only magnify the softness. --}}
        <div class="reveal relative mx-auto flex h-[380px] w-full max-w-[460px] items-center justify-center sm:h-[500px]">
            <div aria-hidden="true" class="absolute size-[300px] rounded-full border border-line sm:size-[440px]"></div>
            <div aria-hidden="true" class="absolute size-[240px] rounded-full border border-accent-line sm:size-[350px]"></div>
            <div aria-hidden="true" class="absolute size-[240px] rotate-[35deg] rounded-full border border-transparent border-t-accent sm:size-[350px]"></div>

            @if ($profile?->photo_path)
                <div class="relative size-[168px] rounded-full bg-[linear-gradient(150deg,var(--c-accent)_0%,var(--c-accent-line)_55%,var(--c-line)_100%)] p-[5px] sm:size-[212px]">
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($profile->photo_path) }}"
                         alt="{{ $user->name }}" width="212" height="212" fetchpriority="high"
                         class="size-full rounded-full object-cover">
                </div>
            @endif

            <span class="absolute right-0 top-10 rounded-lg border border-line bg-surface px-3 py-1.5 font-mono text-[11.5px]">ABAP / OO ABAP</span>
            <span class="absolute bottom-24 left-0 rounded-lg border border-line bg-surface px-3 py-1.5 font-mono text-[11.5px]">OData v2 / v4</span>
            <span class="absolute bottom-6 right-10 rounded-lg border border-accent-line bg-accent-soft px-3 py-1.5 font-mono text-[11.5px] text-accent">Laravel · React</span>
        </div>
    </div>
</section>
