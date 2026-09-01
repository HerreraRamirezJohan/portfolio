@props(['name' => null, 'size' => 18])

@php
    $slug = \App\Support\TechIcon::slug($name);
@endphp

@if ($slug)
    {{-- One drawn set: 24px grid, 1.5 stroke, currentColor so chips tint together. --}}
    <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none"
         stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
         aria-hidden="true" {{ $attributes->merge(['class' => 'shrink-0']) }}>
        @switch($slug)
            @case('php')
                <ellipse cx="12" cy="12" rx="10.5" ry="6"/>
                <path d="M8.2 9.6 7 14.4M8.2 9.6h1.6a1.2 1.2 0 0 1 0 2.4H7.6M13.4 9.6 12.2 14.4M13.4 9.6h1.5a1.2 1.2 0 0 1 0 2.4h-2"/>
                @break

            @case('laravel')
                <path d="M2 7.5 6.2 5l4.2 2.5-4.2 2.5L2 7.5Z"/>
                <path d="m6.2 10 4.2 2.5v5L6.2 15v-5Z"/>
                <path d="m10.4 12.5 4.3-2.5 4.2 2.5-4.2 2.5-4.3-2.5Z"/>
                <path d="m14.7 15 4.2-2.5v5L14.7 20v-5Z"/>
                @break

            @case('python')
                <path d="M12 2.5c-3 0-4.6.9-4.6 2.8v2.4h4.8v.9H5.6C3.6 8.6 2.5 10 2.5 12.6s1 3.9 3.1 3.9h1.5v-2.7c0-2 1.6-3.4 3.6-3.4h3.7c1.8 0 3.1-1.2 3.1-3V5.3c0-1.9-1.6-2.8-4.6-2.8Z"/>
                <path d="M12 21.5c3 0 4.6-.9 4.6-2.8v-2.4h-4.8v-.9h6.6c2 0 3.1-1.4 3.1-4s-1-3.9-3.1-3.9h-1.5v2.7c0 2-1.6 3.4-3.6 3.4h-3.7c-1.8 0-3.1 1.2-3.1 3v2.1c0 1.9 1.6 2.8 4.6 2.8Z"/>
                @break

            @case('java')
                <path d="M13 2c2.5 2.6-3.2 4.3-1.2 7.2"/>
                <path d="M15.5 6.4c2 2.3-2.6 3.7-1 6"/>
                <path d="M6.5 15.5c-2.5 1.4 1 2.6 8 2.2 2.4-.2 4.4-.6 5.5-1.2"/>
                <path d="M8 12.6c-1.6 1.2 1.2 2 6.2 1.7"/>
                <path d="M7 19.4c-1 1.3 2.4 2.1 7.4 1.8 3-.2 5.2-.8 5.6-1.5"/>
                @break

            @case('react')
                <circle cx="12" cy="12" r="1.9" fill="currentColor" stroke="none"/>
                <ellipse cx="12" cy="12" rx="10" ry="4.2"/>
                <ellipse cx="12" cy="12" rx="10" ry="4.2" transform="rotate(60 12 12)"/>
                <ellipse cx="12" cy="12" rx="10" ry="4.2" transform="rotate(120 12 12)"/>
                @break

            @case('javascript')
                <rect x="3" y="3" width="18" height="18" rx="3"/>
                <path d="M10 9.5v5a1.8 1.8 0 0 1-3.5.6"/>
                <path d="M17.5 10.4a2 2 0 0 0-3.4 1.3c0 2 3.4 1.4 3.4 3.2a2 2 0 0 1-3.5 1.1"/>
                @break

            @case('html')
                <path d="M4 3h16l-1.4 16L12 21l-6.6-2L4 3Z"/>
                <path d="M16.5 7.5H8l.4 3.6h7.6l-.5 4.6-3.5 1-3.5-1-.2-2"/>
                @break

            @case('docker')
                <path d="M4 10h3v3H4zM8 10h3v3H8zM12 10h3v3h-3zM8 6.5h3v3H8zM12 6.5h3v3h-3z"/>
                <path d="M1.5 13.5c0 4 3 6.5 8 6.5 7 0 11.5-3.2 12.6-8 2.2.5 4-.4 4.9-1.7"/>
                @break

            @case('linux')
                <path d="M12 2.5c2.2 0 3.2 2 3.2 4.6 0 1.8 2.8 5.4 3.4 8 .5 2.3-.6 4.2-2.2 4.2-1.2 0-1.6-.8-2.4-.8h-4c-.8 0-1.2.8-2.4.8-1.6 0-2.7-1.9-2.2-4.2.6-2.6 3.4-6.2 3.4-8 0-2.6 1-4.6 3.2-4.6Z"/>
                <circle cx="10.4" cy="7.6" r=".9" fill="currentColor" stroke="none"/>
                <circle cx="13.6" cy="7.6" r=".9" fill="currentColor" stroke="none"/>
                @break

            @case('nginx')
                <path d="M12 2.5 21 7.5v9L12 21.5 3 16.5v-9L12 2.5Z"/>
                <path d="M9.5 15.5v-7l5 7v-7"/>
                @break

            @case('git')
                <circle cx="7" cy="17" r="2.4"/>
                <circle cx="7" cy="7" r="2.4"/>
                <circle cx="17.5" cy="11.5" r="2.4"/>
                <path d="M7 9.4v5.2"/>
                <path d="M15.1 12.6c-1.4 2-4 2.3-5.9 1.2"/>
                @break

            @case('github')
                <path d="M9.5 20.5c-4.5 1.4-4.5-2.3-6.3-2.8m12.6 5v-3.6a3 3 0 0 0-.9-2.4c2.9-.3 6-1.5 6-6.4a5 5 0 0 0-1.4-3.5 4.6 4.6 0 0 0-.1-3.5s-1.1-.3-3.7 1.4a12.7 12.7 0 0 0-6.6 0C6.5 3.5 5.4 3.8 5.4 3.8a4.6 4.6 0 0 0-.1 3.5A5 5 0 0 0 3.9 10.9c0 4.8 3 6 5.9 6.4a3 3 0 0 0-.9 2.3v3.6"/>
                @break

            @case('azure')
                <path d="M9.4 3.5h5.3L20.5 20H14L3.5 17.8l6.4-7.6 2.4 6.4"/>
                @break

            @case('postgresql')
                <path d="M17.5 3.5C20 4.4 21.5 7 21.5 10.5c0 4.5-2.4 9.5-4.6 9.5-1 0-1.3-.7-2.4-.7-1.2 0-1.9.7-3 .7-2.4 0-5-5.6-5-10.2C6.5 6 8.6 3.5 11.6 3.5c1.4 0 2.4.6 3.1.6.6 0 1.7-.6 2.8-.6Z"/>
                <path d="M12 8.5v7"/>
                @break

            @case('database')
                <ellipse cx="12" cy="6" rx="8" ry="3"/>
                <path d="M4 6v12c0 1.7 3.6 3 8 3s8-1.3 8-3V6"/>
                <path d="M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3"/>
                @break

            @case('sap')
                {{-- Neutral enterprise glyph: stacked data planes. No SAP wordmark. --}}
                <path d="M12 2.5 21.5 7 12 11.5 2.5 7 12 2.5Z"/>
                <path d="m2.5 12 9.5 4.5 9.5-4.5"/>
                <path d="m2.5 17 9.5 4.5 9.5-4.5"/>
                @break
        @endswitch
    </svg>
@endif
