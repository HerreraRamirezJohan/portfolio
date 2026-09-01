<section id="work" class="border-t border-line bg-elevated px-5 py-24 sm:px-8 sm:py-28 lg:px-12 lg:py-32">
    <div class="mx-auto max-w-[1400px]">
        <x-ui.section-title class="mb-14" :eyebrow="'04 — ' . __('Selected work')" :title="__('Case studies')" />

        <div class="grid gap-6 lg:grid-cols-2" data-reveal-group>
            @foreach ($projects as $project)
                <article class="reveal lift flex flex-col overflow-hidden rounded-2xl border border-line bg-surface">
                    @if ($project->image_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($project->image_path) }}"
                             alt="" loading="lazy"
                             class="h-52 w-full border-b border-line object-cover">
                    @else
                        <div aria-hidden="true"
                             class="h-52 w-full border-b border-line
                                    bg-[linear-gradient(135deg,var(--c-accent-soft)_0%,var(--c-surface)_62%)]"></div>
                    @endif

                    <div class="flex flex-1 flex-col p-7">
                        <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1">
                            <h3 class="text-xl font-semibold">{{ $project->title }}</h3>
                            @if ($project->year)
                                <span class="font-mono text-xs text-faint">{{ $project->year }}</span>
                            @endif
                        </div>

                        @if ($project->summary)
                            <p class="mt-2.5 text-[15px] leading-[1.68] text-muted text-pretty">{{ $project->summary }}</p>
                        @endif

                        @if (filled($project->tech_stack))
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach ($project->tech_stack as $tech)
                                    <span @class([
                                        'flex items-center gap-1.5 rounded-md px-2.5 py-1 font-mono text-[11.5px]',
                                        'border border-accent-line bg-accent-soft text-accent' => \App\Support\TechIcon::isSap($tech),
                                        'border border-line text-muted' => ! \App\Support\TechIcon::isSap($tech),
                                    ])>
                                        <x-ui.tech-icon :name="$tech" :size="13" />
                                        {{ $tech }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        @if ($project->repo_url || $project->live_url)
                            <div class="mt-auto flex flex-wrap items-center gap-x-5 pt-5 text-sm">
                                @if ($project->repo_url)
                                    <a href="{{ $project->repo_url }}" rel="noopener noreferrer" target="_blank"
                                       class="flex items-center gap-1.5 text-muted underline underline-offset-4 transition-colors hover:text-accent">
                                        {{ __('Repository') }}
                                    </a>
                                @endif
                                @if ($project->live_url)
                                    <a href="{{ $project->live_url }}" rel="noopener noreferrer" target="_blank"
                                       class="flex items-center gap-1.5 text-muted underline underline-offset-4 transition-colors hover:text-accent">
                                        {{ __('Live') }}
                                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                             stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M7 17 17 7M9 7h8v8"/>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
