@if ($user->workExperiences->isNotEmpty())
    <section id="experience" class="px-5 py-24 sm:px-8 sm:py-28 lg:px-12 lg:py-32">
        <div class="mx-auto max-w-[1400px]">
            <x-ui.section-title class="mb-14" :eyebrow="'03 — ' . __('Experience')" :title="__('Close to the business')" />

            <div class="relative pl-8 sm:pl-11" data-timeline data-reveal-group>
                {{-- Rail sits behind; the drawn line scales over it on scroll. --}}
                <div aria-hidden="true" class="absolute bottom-10 left-[5px] top-2 w-px bg-line"></div>
                <div aria-hidden="true" data-timeline-line
                     class="timeline-line absolute bottom-10 left-[5px] top-2 w-px
                            bg-[linear-gradient(180deg,var(--c-accent)_0%,var(--c-accent-line)_55%,var(--c-line)_100%)]"></div>

                @foreach ($user->workExperiences as $job)
                    @php $lead = $loop->first; @endphp

                    <article class="reveal relative pb-14 last:pb-0">
                        <span aria-hidden="true" @class([
                            'absolute -left-8 top-1.5 size-[11px] rounded-full sm:-left-11',
                            'bg-accent ring-4 ring-accent-soft' => $lead,
                            'bg-accent/55' => $loop->index === 1,
                            'bg-faint/40' => $loop->index > 1,
                        ])></span>

                        <div class="flex flex-wrap items-baseline justify-between gap-x-5 gap-y-1">
                            <h3 @class([
                                'font-semibold',
                                'text-[22px] sm:text-2xl' => $lead,
                                'text-xl text-ink/90' => ! $lead,
                            ])>{{ $job->company }}</h3>
                            <span class="font-mono text-[12.5px] text-faint">{{ $job->dateRange() }}</span>
                        </div>

                        <div class="mt-1.5 flex flex-wrap items-center gap-x-3 gap-y-1">
                            <p @class(['text-[15.5px]', 'text-accent' => $lead, 'text-muted' => ! $lead])>{{ $job->role }}</p>
                            @if ($job->location)
                                <span aria-hidden="true" class="text-faint/60">·</span>
                                <span class="text-sm text-faint">{{ $job->location }}</span>
                            @endif
                        </div>

                        @if (filled($job->bullets))
                            <div class="mt-5 grid gap-x-8 gap-y-3.5 md:grid-cols-2">
                                @foreach ($job->bullets as $bullet)
                                    <p class="text-[15px] leading-[1.68] text-muted text-pretty">
                                        @if (filled($bullet['label'] ?? null))
                                            <strong class="font-semibold text-ink">{{ $bullet['label'] }}:</strong>
                                        @endif
                                        {{ $bullet['body'] }}
                                    </p>
                                @endforeach
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
