@if ($user->educations->isNotEmpty() || $user->languages->isNotEmpty())
    <section class="px-5 pb-24 sm:px-8 sm:pb-28 lg:px-12 lg:pb-32">
        <div class="mx-auto grid max-w-[1400px] gap-14 border-t border-line pt-14 md:grid-cols-2 md:gap-18"
             data-reveal-group>

            @if ($user->educations->isNotEmpty())
                <div class="reveal">
                    <h2 class="mb-6 font-mono text-xs uppercase tracking-[0.12em] text-accent">{{ __('Education') }}</h2>

                    <div class="flex flex-col gap-6">
                        @foreach ($user->educations as $education)
                            <div>
                                <div class="flex flex-wrap items-baseline justify-between gap-x-5 gap-y-1">
                                    <h3 class="text-[19px] font-semibold">{{ $education->institution }}</h3>
                                    <span class="font-mono text-[12.5px] text-faint">
                                        {{ collect([$education->start_year, $education->end_year])->filter()->implode(' – ') }}
                                    </span>
                                </div>
                                <p class="mt-1.5 text-[15.5px] text-muted">{{ $education->degree }}</p>
                                @if ($education->notes)
                                    <p class="mt-2 text-[15px] leading-[1.7] text-muted text-pretty">{{ $education->notes }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($user->languages->isNotEmpty())
                <div class="reveal">
                    <h2 class="mb-6 font-mono text-xs uppercase tracking-[0.12em] text-accent">{{ __('Languages') }}</h2>

                    <div class="flex flex-col gap-3.5">
                        @foreach ($user->languages as $language)
                            <div class="flex items-baseline justify-between gap-5 border-b border-line-soft pb-3.5 last:border-0">
                                <span class="text-base font-medium">{{ $language->name }}</span>
                                <span class="font-mono text-[12.5px] text-faint">{{ $language->level_label }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
@endif
