@php
    $locales = config('portfolio.locales');
    $current = app()->getLocale();
    $projects = $user->projects;
@endphp

<div>
    @include('partials.portfolio.nav', ['locales' => $locales, 'current' => $current, 'hasProjects' => $projects->isNotEmpty()])

    <main id="main">
        @include('partials.portfolio.hero')
        @include('partials.portfolio.marquee')
        @include('partials.portfolio.approach')
        @include('partials.portfolio.sap')
        @include('partials.portfolio.experience')

        @if ($projects->isNotEmpty())
            @include('partials.portfolio.projects', ['projects' => $projects])
        @endif

        @include('partials.portfolio.skills')
        @include('partials.portfolio.education')
        @include('partials.portfolio.contact')
    </main>

    @include('partials.portfolio.footer')
</div>
