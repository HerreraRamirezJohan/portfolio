/*
| Structural heading in heavy expanded caps (.macro in app.css).
|
| `fit` sizes the type so the longest word spans the container at most,
| capped at `max` px. Words wrap naturally, so "Backend developer." breaks
| between words, and long Spanish words never overflow on a phone. Without
| `fit` the size comes from `className`.
|
| A trailing full stop is painted in signal red.
*/

// Average advance of one uppercase glyph at wdth 125 / weight 800, in em,
// with the -0.04em tracking and a little slack.
const GLYPH_EM = 0.84;

export function Macro({ as: Tag = 'h2', text, fit = false, max = 150, className = '', ...props }) {
    const stop = text.endsWith('.');
    const body = stop ? text.slice(0, -1) : text;

    const heading = (
        <Tag
            className={`macro text-balance ${className}`}
            style={fit ? { fontSize: `min(${max}px, calc(100cqi / ${longestWord(text) * GLYPH_EM}))` } : undefined}
            {...props}
        >
            {body}
            {stop && <span className="text-signal">.</span>}
        </Tag>
    );

    return fit ? <div className="@container">{heading}</div> : heading;
}

function longestWord(text) {
    return Math.max(...text.split(/\s+/).map((word) => word.length));
}
