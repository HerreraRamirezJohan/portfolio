<!DOCTYPE html>
{{-- `no-js` is removed by the inline script below; the CSS uses it so that
     reveal-on-scroll content is never hidden when JS does not run. --}}
<html lang="{{ app()->getLocale() }}" class="no-js scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>

    @if (isset($description))
        <meta name="description" content="{{ $description }}">
        <meta property="og:description" content="{{ $description }}">
    @endif

    <meta property="og:title" content="{{ $title ?? config('app.name') }}">
    <meta property="og:type" content="profile">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary">

    @foreach (config('portfolio.locales') as $locale)
        <link rel="alternate" hreflang="{{ $locale }}" href="{{ route('portfolio', $locale) }}">
    @endforeach

    {{-- Runs before first paint: sets the theme class so there is no flash of
         the wrong palette, and drops `no-js`. Kept inline and tiny on purpose. --}}
    <script>
        (function () {
            var el = document.documentElement;
            el.classList.remove('no-js');

            try {
                var saved = localStorage.getItem('theme');
                var dark = saved
                    ? saved === 'dark'
                    : window.matchMedia('(prefers-color-scheme: dark)').matches;

                if (dark) {
                    el.classList.add('dark');
                }
            } catch (e) {
                // Storage blocked -- fall back to the OS preference only.
                if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    el.classList.add('dark');
                }
            }
        })();
    </script>

    {{-- Emits the @font-face rules and the --font-* custom properties that
         app.css builds the type scale on. Without this the font utilities
         reference undefined variables and silently fall back to system UI. --}}
    {{ Vite::fonts() }}

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-base font-sans text-ink antialiased">
    <a href="#main"
       class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-lg focus:bg-accent focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-accent-ink">
        {{ __('Skip to content') }}
    </a>

    {{ $slot }}
</body>
</html>
