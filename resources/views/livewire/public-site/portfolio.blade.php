@php
    $locales = config('portfolio.locales');
    $current = app()->getLocale();
@endphp

<div class="mx-auto max-w-3xl px-6 py-16 sm:py-24">

    {{-- Language switcher --}}
    <nav class="mb-12 flex justify-end gap-1 text-sm" aria-label="Language">
        @foreach ($locales as $locale)
            <a href="{{ route('portfolio', $locale) }}"
               @class([
                   'rounded px-2.5 py-1 font-medium transition',
                   'bg-stone-800 text-stone-50 dark:bg-stone-200 dark:text-stone-900' => $locale === $current,
                   'text-stone-500 hover:text-stone-900 dark:text-stone-400 dark:hover:text-stone-100' => $locale !== $current,
               ])
               @if ($locale === $current) aria-current="true" @endif>
                {{ strtoupper($locale) }}
            </a>
        @endforeach
    </nav>

    {{-- Hero --}}
    <header class="border-b border-stone-200 pb-10 dark:border-stone-800">
        <h1 class="text-3xl font-semibold tracking-tight text-stone-900 sm:text-4xl dark:text-stone-50">
            {{ $user->name }}
        </h1>

        @if ($profile?->headline)
            <p class="mt-2 text-lg text-stone-600 dark:text-stone-400">{{ $profile->headline }}</p>
        @endif

        <div class="mt-5 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-stone-600 dark:text-stone-400">
            @if ($profile?->location)
                <span>{{ $profile->location }}</span>
            @endif
            @if ($profile?->phone)
                <a class="hover:text-stone-900 dark:hover:text-stone-100" href="tel:{{ preg_replace('/\s+/', '', $profile->phone) }}">
                    {{ $profile->phone }}
                </a>
            @endif
            @if ($profile?->public_email)
                <a class="hover:text-stone-900 dark:hover:text-stone-100" href="mailto:{{ $profile->public_email }}">
                    {{ $profile->public_email }}
                </a>
            @endif
            @if ($profile?->linkedin_url)
                <a class="hover:text-stone-900 dark:hover:text-stone-100" href="{{ $profile->linkedin_url }}" rel="noopener noreferrer" target="_blank">
                    LinkedIn
                </a>
            @endif
            @if ($profile?->github_url)
                <a class="hover:text-stone-900 dark:hover:text-stone-100" href="{{ $profile->github_url }}" rel="noopener noreferrer" target="_blank">
                    GitHub
                </a>
            @endif
        </div>
    </header>

    {{-- Summary --}}
    @if ($profile?->summary)
        <section class="mt-10">
            <x-section-heading>{{ __('Profile') }}</x-section-heading>
            <p class="mt-4 text-[0.95rem] leading-relaxed text-stone-700 dark:text-stone-300">
                {{ $profile->summary }}
            </p>
        </section>
    @endif

    {{-- Experience --}}
    @if ($user->workExperiences->isNotEmpty())
        <section class="mt-12">
            <x-section-heading>{{ __('Experience') }}</x-section-heading>

            <div class="mt-6 space-y-9">
                @foreach ($user->workExperiences as $job)
                    <article>
                        <div class="flex flex-wrap items-baseline justify-between gap-x-4">
                            <h3 class="font-semibold text-stone-900 dark:text-stone-50">{{ $job->company }}</h3>
                            <span class="text-sm text-stone-500 dark:text-stone-400">{{ $job->location }}</span>
                        </div>
                        <div class="mt-0.5 flex flex-wrap items-baseline justify-between gap-x-4">
                            <p class="italic text-stone-700 dark:text-stone-300">{{ $job->role }}</p>
                            <span class="text-sm text-stone-500 dark:text-stone-400">{{ $job->dateRange() }}</span>
                        </div>

                        @if (filled($job->bullets))
                            <ul class="mt-3 space-y-2">
                                @foreach ($job->bullets as $bullet)
                                    <li class="flex gap-2.5 text-[0.95rem] leading-relaxed text-stone-700 dark:text-stone-300">
                                        <span aria-hidden="true" class="mt-[0.55em] size-1 shrink-0 rounded-full bg-stone-400 dark:bg-stone-600"></span>
                                        <span>
                                            @if (filled($bullet['label'] ?? null))
                                                <strong class="font-semibold text-stone-900 dark:text-stone-100">{{ $bullet['label'] }}:</strong>
                                            @endif
                                            {{ $bullet['body'] }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Education --}}
    @if ($user->educations->isNotEmpty())
        <section class="mt-12">
            <x-section-heading>{{ __('Education') }}</x-section-heading>

            <div class="mt-6 space-y-6">
                @foreach ($user->educations as $education)
                    <article>
                        <div class="flex flex-wrap items-baseline justify-between gap-x-4">
                            <h3 class="font-semibold text-stone-900 dark:text-stone-50">{{ $education->institution }}</h3>
                            <span class="text-sm text-stone-500 dark:text-stone-400">{{ $education->location }}</span>
                        </div>
                        <div class="mt-0.5 flex flex-wrap items-baseline justify-between gap-x-4">
                            <p class="italic text-stone-700 dark:text-stone-300">{{ $education->degree }}</p>
                            <span class="text-sm text-stone-500 dark:text-stone-400">
                                {{ collect([$education->start_year, $education->end_year])->filter()->implode(' – ') }}
                            </span>
                        </div>
                        @if ($education->notes)
                            <p class="mt-2 text-[0.95rem] leading-relaxed text-stone-700 dark:text-stone-300">{{ $education->notes }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Skills --}}
    @if ($user->skillGroups->isNotEmpty())
        <section class="mt-12">
            <x-section-heading>{{ __('Technical Skills') }}</x-section-heading>

            <div class="mt-6 space-y-6">
                @foreach ($user->skillGroups as $group)
                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-stone-900 dark:text-stone-100">
                            {{ $group->label }}
                        </h3>

                        @if ($group->skills->isNotEmpty())
                            <ul class="mt-2.5 flex flex-wrap gap-1.5">
                                @foreach ($group->skills as $skill)
                                    <li class="rounded-md bg-stone-200/70 px-2 py-0.5 text-[0.8rem] text-stone-700 dark:bg-stone-800 dark:text-stone-300">
                                        {{ $skill->name }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if ($group->description)
                            <p class="mt-2 text-sm leading-relaxed text-stone-600 dark:text-stone-400">{{ $group->description }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Languages --}}
    @if ($user->languages->isNotEmpty())
        <section class="mt-12">
            <x-section-heading>{{ __('Languages') }}</x-section-heading>

            <ul class="mt-4 space-y-1.5">
                @foreach ($user->languages as $language)
                    <li class="text-[0.95rem] text-stone-700 dark:text-stone-300">
                        <span class="font-medium text-stone-900 dark:text-stone-100">{{ $language->name }}</span>
                        <span class="text-stone-500 dark:text-stone-400">— {{ $language->level_label }}</span>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- Projects --}}
    @if ($user->projects->isNotEmpty())
        <section class="mt-12">
            <x-section-heading>{{ __('Projects') }}</x-section-heading>

            <div class="mt-6 space-y-6">
                @foreach ($user->projects as $project)
                    <article>
                        <h3 class="font-semibold text-stone-900 dark:text-stone-50">{{ $project->title }}</h3>
                        @if ($project->summary)
                            <p class="mt-1 text-[0.95rem] leading-relaxed text-stone-700 dark:text-stone-300">{{ $project->summary }}</p>
                        @endif
                        <div class="mt-2 flex flex-wrap gap-x-4 text-sm">
                            @if ($project->repo_url)
                                <a class="text-stone-600 underline underline-offset-2 hover:text-stone-900 dark:text-stone-400 dark:hover:text-stone-100"
                                   href="{{ $project->repo_url }}" rel="noopener noreferrer" target="_blank">Repository</a>
                            @endif
                            @if ($project->live_url)
                                <a class="text-stone-600 underline underline-offset-2 hover:text-stone-900 dark:text-stone-400 dark:hover:text-stone-100"
                                   href="{{ $project->live_url }}" rel="noopener noreferrer" target="_blank">Live</a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <footer class="mt-16 border-t border-stone-200 pt-6 text-sm text-stone-500 dark:border-stone-800 dark:text-stone-400">
        <p>&copy; {{ now()->year }} {{ $user->name }}</p>
    </footer>
</div>
