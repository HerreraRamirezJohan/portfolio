<!DOCTYPE html>
{{-- The one Blade view: a shell for the React app in resources/js. Only the
     <head> is server-rendered, so crawlers and link previews get the title,
     description and hreflang tags without running JavaScript. --}}
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>

    @if (filled($description ?? null))
        <meta name="description" content="{{ $description }}">
        <meta property="og:description" content="{{ $description }}">
    @endif

    <meta property="og:title" content="{{ $title ?? config('app.name') }}">
    <meta property="og:type" content="profile">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary">

    @foreach ($alternates ?? [] as $locale => $url)
        <link rel="alternate" hreflang="{{ $locale }}" href="{{ $url }}">
    @endforeach

    {{-- Runs before first paint: sets the theme class so there is no flash of
         the wrong palette. Kept inline and tiny on purpose. --}}
    <script>
        (function () {
            var el = document.documentElement;
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            try {
                var saved = localStorage.getItem('theme');
                if (saved ? saved === 'dark' : prefersDark) {
                    el.classList.add('dark');
                }
            } catch (e) {
                // Storage blocked -- fall back to the OS preference only.
                if (prefersDark) {
                    el.classList.add('dark');
                }
            }
        })();
    </script>

    {{-- Emits the @font-face rules and the --font-* custom properties that
         app.css builds the type scale on. --}}
    {{ Vite::fonts() }}

    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
</head>
<body class="min-h-screen bg-base font-sans text-ink antialiased">
    <div id="app" data-config='@json($config)'></div>

    <noscript>
        <p class="p-8">{{ __('This site needs JavaScript to display its content.') }}</p>
    </noscript>
</body>
</html>
