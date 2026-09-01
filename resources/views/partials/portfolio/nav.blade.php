@php
    // "Work" only appears once there is something to link to.
    $links = [
        '#home' => __('Home'),
        '#approach' => __('Approach'),
        '#sap' => 'SAP',
        '#experience' => __('Experience'),
    ];

    if ($hasProjects) {
        $links['#work'] = __('Work');
    }

    $links['#contact'] = __('Contact');
@endphp

<header class="fixed inset-x-0 top-0 z-50 border-b border-line bg-base/72 backdrop-blur-md">
    <nav class="mx-auto flex h-16 max-w-[1400px] items-center justify-between gap-4 px-5 sm:px-8 lg:px-12"
         aria-label="{{ __('Main') }}">

        <a href="#home" class="flex shrink-0 items-center gap-2.5">
            <span class="flex size-7 items-center justify-center rounded-full border-[1.5px] border-accent font-mono text-[11px] font-semibold text-accent">
                J
            </span>
            <span class="font-mono text-[13px] tracking-wide text-ink">johan.dev</span>
        </a>

        <div class="hidden items-center gap-7 text-sm lg:flex">
            @foreach ($links as $href => $label)
                <a href="{{ $href }}" data-nav-link
                   class="text-muted transition-colors hover:text-ink">{{ $label }}</a>
            @endforeach
        </div>

        <div class="flex shrink-0 items-center gap-2.5">
            {{-- Language --}}
            <div class="flex items-center gap-0.5 rounded-full border border-line p-0.5 font-mono text-xs">
                @foreach ($locales as $locale)
                    <a href="{{ route('portfolio', $locale) }}"
                       @class([
                           'rounded-full px-2.5 py-1 transition-colors',
                           'bg-accent font-semibold text-accent-ink' => $locale === $current,
                           'text-faint hover:text-ink' => $locale !== $current,
                       ])
                       @if ($locale === $current) aria-current="true" @endif>
                        {{ strtoupper($locale) }}
                    </a>
                @endforeach
            </div>

            {{-- Theme --}}
            <button type="button" data-theme-toggle aria-pressed="false"
                    class="flex size-9 items-center justify-center rounded-lg border border-line text-muted transition-colors hover:text-ink"
                    aria-label="{{ __('Toggle theme') }}">
                <svg class="size-4 dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.6" stroke-linecap="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="4"/>
                    <path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>
                </svg>
                <svg class="hidden size-4 dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.6" stroke-linecap="round" aria-hidden="true">
                    <path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z"/>
                </svg>
            </button>
        </div>
    </nav>
</header>
