/*
| UI glyphs: 24px grid, round caps, currentColor.
*/

const glyphs = {
    'arrow-right': (
        <>
            <path d="M5 12h14" />
            <path d="m13 6 6 6-6 6" />
        </>
    ),
    'arrow-left': (
        <>
            <path d="M19 12H5" />
            <path d="m11 6-6 6 6 6" />
        </>
    ),
    'arrow-up-right': (
        <>
            <path d="M7 17 17 7" />
            <path d="M9 7h8v8" />
        </>
    ),
    sun: (
        <>
            <circle cx="12" cy="12" r="4" />
            <path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4" />
        </>
    ),
    moon: <path d="M20 14.5A8 8 0 0 1 9.5 4a8 8 0 1 0 10.5 10.5Z" />,
    menu: (
        <>
            <path d="M4 8h16" />
            <path d="M4 16h16" />
        </>
    ),
    sap: (
        <>
            <path d="M12 2.5 2.5 7.5 12 12.5l9.5-5Z" />
            <path d="m2.5 12 9.5 5 9.5-5" />
            <path d="m2.5 16.5 9.5 5 9.5-5" />
        </>
    ),
    web: (
        <>
            <path d="m8 7-5 5 5 5" />
            <path d="m16 7 5 5-5 5" />
            <path d="m13.5 4-3 16" />
        </>
    ),
    infra: (
        <>
            <rect x="3" y="3.5" width="18" height="7" rx="2" />
            <rect x="3" y="13.5" width="18" height="7" rx="2" />
            <path d="M7 7h.01" />
            <path d="M7 17h.01" />
        </>
    ),
};

export function Icon({ name, size = 18, stroke = 1.7, className = '' }) {
    return (
        <svg
            width={size}
            height={size}
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth={stroke}
            strokeLinecap="round"
            strokeLinejoin="round"
            aria-hidden="true"
            className={`shrink-0 ${className}`}
        >
            {glyphs[name]}
        </svg>
    );
}
