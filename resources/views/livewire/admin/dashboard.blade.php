<div>
    <h1 class="text-xl font-semibold text-stone-900 dark:text-stone-50">{{ __('Dashboard') }}</h1>
    <p class="mt-1 text-sm text-stone-600 dark:text-stone-400">{{ $user->name }} — {{ $user->email }}</p>

    @php
        $cards = [
            'admin.experience' => [__('Experience'), $user->work_experiences_count],
            'admin.education' => [__('Education'), $user->educations_count],
            'admin.skills' => [__('Technical Skills'), $user->skill_groups_count],
            'admin.languages' => [__('Languages'), $user->languages_count],
            'admin.projects' => [__('Projects'), $user->projects_count],
        ];
    @endphp

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($cards as $route => [$label, $count])
            <a class="rounded-lg border border-stone-200 bg-white p-4 transition hover:border-stone-400 dark:border-stone-800 dark:bg-stone-900 dark:hover:border-stone-600"
               href="{{ route($route) }}">
                <p class="text-sm text-stone-500 dark:text-stone-400">{{ $label }}</p>
                <p class="mt-1 text-2xl font-semibold text-stone-900 dark:text-stone-50">{{ $count }}</p>
            </a>
        @endforeach
    </div>
</div>
