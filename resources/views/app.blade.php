<!DOCTYPE html>
{{-- The one Blade view: a shell for the React app in resources/js. Only the
     <head> is server-rendered, so crawlers and link previews get the title,
     description and hreflang tags without running JavaScript. --}}
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Browser chrome colour: the public site's --c-base. --}}
    <meta name="theme-color" content="#0e0e0d">
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

    {{-- Admin only (the public site is dark by design): follow the OS theme,
         set before first paint so there is no flash. --}}
    <script>
        if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
        }
    </script>

    {{-- Emits the @font-face rules and the --font-* custom properties that
         app.css builds the type scale on. --}}
    {{ Vite::fonts() }}

    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
</head>
<body class="min-h-screen bg-base font-sans text-ink antialiased">
    <div id="app" data-config='@json($config)'></div>

    {{-- This page's own API payload, so React renders without a first request.
         PublicContent hex-escapes <, >, & and quotes, so it cannot close the tag. --}}
    @isset($bootstrap)
        <script type="application/json" id="bootstrap" data-url="{{ $bootstrap['url'] }}">{!! $bootstrap['json'] !!}</script>
    @endisset

    <noscript>
        <p class="p-8">{{ __('This site needs JavaScript to display its content.') }}</p>
    </noscript>
</body>
</html>
