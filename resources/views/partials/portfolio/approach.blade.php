@php
    // The pull quote is the sentence in the summary that carries the point;
    // the rest becomes the body. Falls back to showing the summary whole.
    $summary = $profile?->summary;
    $sentences = $summary ? preg_split('/(?<=\.)\s+/', trim($summary)) : [];
    $quote = $sentences[1] ?? null;
    $body = $summary;

    // Editorial framing, not CV data -- these live in lang/{locale}.json so
    // they stay translatable and editable without a migration.
    $principles = [
        __('Read the spec end to end') => __('Every functional specification, before estimating - looking for the holes, usually in how data is meant to be obtained, or in business rules assumed but never written down.'),
        __('Walk the real workflow') => __('With the people who will use the system, validating my understanding against what they actually do day to day, and putting two or three options on the table before writing code.'),
        __('Stay close to the business') => __('Working next to the areas that own the process, which is where I learned to think about the process first and the screen second.'),
    ];
@endphp

@if ($summary)
    <section id="approach" class="px-5 py-24 sm:px-8 sm:py-28 lg:px-12 lg:py-32">
        <div class="mx-auto grid max-w-[1400px] gap-14 lg:grid-cols-[0.85fr_1.15fr] lg:gap-22">

            <div>
                <x-ui.section-title :eyebrow="'01 — ' . __('Approach')" :title="__('Process first, screen second')" />

                @if ($quote)
                    <blockquote class="reveal mt-8 border-l-2 border-accent-line pl-5">
                        <p class="font-display text-[21px] italic leading-[1.5] text-muted text-pretty">{{ $quote }}</p>
                    </blockquote>
                @endif
            </div>

            <div data-reveal-group>
                <p class="reveal mb-8 text-[16px] leading-[1.75] text-muted text-pretty">{{ $body }}</p>

                <div class="flex flex-col">
                    @foreach ($principles as $heading => $detail)
                        <div class="reveal grid grid-cols-[auto_1fr] gap-x-6 border-t border-line py-7 last:border-b">
                            <span class="pt-1 font-mono text-xs text-accent">/{{ str_pad((string) ($loop->index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <div>
                                <h3 class="mb-2 text-[19px] font-semibold">{{ $heading }}</h3>
                                <p class="text-[15.5px] leading-[1.7] text-muted text-pretty">{{ $detail }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif
