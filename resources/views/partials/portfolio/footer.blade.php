@php
    $socials = array_filter([
        'GitHub' => $profile?->github_url,
        'LinkedIn' => $profile?->linkedin_url,
        'Website' => $profile?->website_url,
    ]);
@endphp

<footer class="border-t border-line px-5 py-9 sm:px-8 lg:px-12">
    <div class="mx-auto flex max-w-[1400px] flex-col items-center justify-between gap-4 sm:flex-row">
        <p class="font-mono text-[12.5px] text-faint">© {{ now()->year }} {{ $user->name }}</p>

        <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 font-mono text-[12.5px] text-faint">
            @foreach ($socials as $label => $url)
                <a href="{{ $url }}" rel="noopener noreferrer" target="_blank"
                   class="transition-colors hover:text-accent">{{ $label }}</a>
            @endforeach

            @if ($profile?->location)
                <span>{{ $profile->location }}</span>
            @endif
        </div>
    </div>
</footer>
