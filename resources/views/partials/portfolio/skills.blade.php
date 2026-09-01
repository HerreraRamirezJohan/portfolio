@if ($user->skillGroups->isNotEmpty())
    <section id="skills" class="px-5 py-24 sm:px-8 sm:py-28 lg:px-12 lg:py-32">
        <div class="mx-auto max-w-[1400px]">
            <x-ui.section-title class="mb-14" :eyebrow="'05 — ' . __('Toolkit')" :title="__('What I build with')" />

            <div class="grid gap-x-16 gap-y-11 md:grid-cols-2" data-reveal-group>
                @foreach ($user->skillGroups as $group)
                    <div class="reveal">
                        <h3 class="mb-4 border-b border-line pb-3 font-mono text-xs uppercase tracking-[0.12em] text-faint">
                            {{ $group->label }}
                        </h3>

                        @if ($group->skills->isNotEmpty())
                            <ul class="flex flex-wrap gap-2">
                                @foreach ($group->skills as $skill)
                                    <li @class([
                                        'flex items-center gap-2 rounded-lg border px-3 py-1.5 text-sm',
                                        'border-accent-line bg-accent-soft text-accent' => \App\Support\TechIcon::isSap($skill->name),
                                        'border-line bg-surface' => ! \App\Support\TechIcon::isSap($skill->name),
                                    ])>
                                        <x-ui.tech-icon :name="$skill->name" :size="15" />
                                        {{ $skill->name }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if ($group->description)
                            <p class="mt-3.5 text-sm leading-[1.7] text-muted text-pretty">{{ $group->description }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
