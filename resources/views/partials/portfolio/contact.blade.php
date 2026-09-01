<section id="contact" class="relative overflow-hidden border-t border-line bg-elevated px-5 py-24 sm:px-8 sm:py-28 lg:px-12 lg:py-32">
    <div aria-hidden="true"
         class="pointer-events-none absolute -bottom-56 left-1/2 size-[780px] max-w-[130vw] -translate-x-1/2 rounded-full
                bg-[radial-gradient(circle,var(--c-accent-soft)_0%,transparent_68%)]"></div>

    <div class="relative mx-auto max-w-3xl text-center" data-reveal-group>
        <p class="reveal mb-5 font-mono text-[11.5px] uppercase tracking-[0.16em] text-accent">
            06 — {{ __('Contact') }}
        </p>

        {{-- One string with the emphasis inline: splitting it into fragments
             breaks in Spanish, where the adjective follows the noun. The
             markup is ours, not user input, so unescaped output is safe. --}}
        <h2 class="reveal font-display text-[clamp(2.25rem,5.5vw,3.875rem)] font-normal leading-[1.06] tracking-[-0.02em] text-pretty [&_em]:italic [&_em]:text-accent">
            {!! __('Have a process that needs a <em>careful</em> pair of hands?') !!}
        </h2>

        @if ($profile?->location)
            <p class="reveal mx-auto mt-6 max-w-lg text-[16.5px] leading-[1.7] text-muted text-pretty">
                {{ __('Based in :location, open to enterprise and full-stack work.', ['location' => $profile->location]) }}
            </p>
        @endif

        <div class="reveal mt-10 flex flex-wrap items-center justify-center gap-3.5">
            @if ($profile?->public_email)
                <a href="mailto:{{ $profile->public_email }}"
                   class="flex h-[54px] items-center gap-2.5 rounded-[10px] bg-accent px-6 text-[15px] font-semibold text-accent-ink transition-opacity hover:opacity-90">
                    <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="2.5" y="4.5" width="19" height="15" rx="2.5"/><path d="m3 6.5 9 6 9-6"/>
                    </svg>
                    <span class="truncate">{{ $profile->public_email }}</span>
                </a>
            @endif

            @if ($profile?->phone)
                <a href="tel:{{ preg_replace('/\s+/', '', $profile->phone) }}"
                   class="flex h-[54px] items-center gap-2.5 rounded-[10px] border border-line px-6 text-[15px] font-medium transition-colors hover:border-accent-line">
                    <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M6.5 3.5h-2a2 2 0 0 0-2 2.2c.6 7.3 6.5 13.2 13.8 13.8a2 2 0 0 0 2.2-2v-2a1.5 1.5 0 0 0-1.2-1.5l-2.6-.5a1.5 1.5 0 0 0-1.5.7l-.8 1.3a12 12 0 0 1-5.4-5.4l1.3-.8a1.5 1.5 0 0 0 .7-1.5l-.5-2.6A1.5 1.5 0 0 0 6.5 3.5Z"/>
                    </svg>
                    {{ $profile->phone }}
                </a>
            @endif
        </div>
    </div>
</section>
