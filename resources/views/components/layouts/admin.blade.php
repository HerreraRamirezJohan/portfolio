<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? __('Admin') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-100 text-stone-800 antialiased dark:bg-stone-950 dark:text-stone-200">

@auth
    @php
        $nav = [
            'admin.dashboard' => __('Dashboard'),
            'admin.profile' => __('Profile'),
            'admin.experience' => __('Experience'),
            'admin.education' => __('Education'),
            'admin.skills' => __('Technical Skills'),
            'admin.languages' => __('Languages'),
            'admin.projects' => __('Projects'),
        ];
    @endphp

    <header class="border-b border-stone-200 bg-white dark:border-stone-800 dark:bg-stone-900">
        <div class="mx-auto flex max-w-5xl flex-wrap items-center gap-x-6 gap-y-3 px-6 py-3">
            <a class="font-semibold text-stone-900 dark:text-stone-50" href="{{ route('admin.dashboard') }}">
                {{ __('Admin') }}
            </a>

            <nav class="flex flex-wrap gap-x-4 gap-y-1 text-sm">
                @foreach ($nav as $route => $label)
                    <a href="{{ route($route) }}"
                       @class([
                           'transition hover:text-stone-900 dark:hover:text-stone-100',
                           'font-medium text-stone-900 dark:text-stone-100' => request()->routeIs($route),
                           'text-stone-500 dark:text-stone-400' => ! request()->routeIs($route),
                       ])>{{ $label }}</a>
                @endforeach
            </nav>

            <div class="ml-auto flex items-center gap-4 text-sm">
                <a class="text-stone-500 underline underline-offset-2 hover:text-stone-900 dark:text-stone-400 dark:hover:text-stone-100"
                   href="{{ route('portfolio', config('app.locale')) }}" target="_blank">{{ __('View site') }}</a>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button class="text-stone-500 hover:text-stone-900 dark:text-stone-400 dark:hover:text-stone-100" type="submit">
                        {{ __('Log out') }}
                    </button>
                </form>
            </div>
        </div>
    </header>
@endauth

<main class="mx-auto max-w-5xl px-6 py-10">
    {{ $slot }}
</main>

</body>
</html>
