/*
| Public-site behaviour: scroll reveals, the timeline draw, nav highlighting
| and the theme toggle. Deliberately dependency-free -- none of it is worth a
| library, and the page must stay useful if this file never runs.
*/

const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');

/**
 * Reveal elements as they enter the viewport, staggered within each group.
 *
 * Elements carry `.reveal` (hidden by CSS). If IntersectionObserver is
 * missing, everything is shown at once rather than left invisible.
 */
function initReveals() {
    const items = document.querySelectorAll('.reveal');

    if (! items.length) {
        return;
    }

    if (reduced.matches || ! ('IntersectionObserver' in window)) {
        items.forEach((el) => el.classList.add('is-visible'));

        return;
    }

    // Index within the parent so siblings stagger, not the whole page.
    document.querySelectorAll('[data-reveal-group]').forEach((group) => {
        group.querySelectorAll('.reveal').forEach((el, i) => {
            el.dataset.revealIndex = String(i);
        });
    });

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (! entry.isIntersecting) {
                return;
            }

            const el = entry.target;
            const index = Number(el.dataset.revealIndex ?? 0);

            el.style.setProperty('--reveal-delay', `${index * 70}ms`);
            el.classList.add('is-visible');

            // One-shot: re-animating on scroll-back is noise.
            observer.unobserve(el);
        });
    }, { rootMargin: '0px 0px -12% 0px', threshold: 0.1 });

    items.forEach((el) => observer.observe(el));
}

/**
 * Scale the timeline connector from 0 to 1 as its section scrolls through.
 */
function initTimeline() {
    const line = document.querySelector('[data-timeline-line]');
    const track = line ? line.closest('[data-timeline]') : null;

    if (! line || ! track || reduced.matches) {
        return;
    }

    let ticking = false;

    const draw = () => {
        const rect = track.getBoundingClientRect();
        const start = window.innerHeight * 0.85;
        const distance = rect.height + start - window.innerHeight * 0.25;
        const progress = distance > 0 ? (start - rect.top) / distance : 1;

        line.style.setProperty('--draw', String(Math.min(1, Math.max(0, progress))));
        ticking = false;
    };

    const onScroll = () => {
        if (ticking) {
            return;
        }

        ticking = true;
        requestAnimationFrame(draw);
    };

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll, { passive: true });
    draw();
}

/**
 * Mark the nav link whose section is currently in view.
 */
function initNav() {
    const links = [...document.querySelectorAll('[data-nav-link]')];
    const sections = links
        .map((link) => document.querySelector(link.getAttribute('href')))
        .filter(Boolean);

    if (! sections.length || ! ('IntersectionObserver' in window)) {
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (! entry.isIntersecting) {
                return;
            }

            links.forEach((link) => {
                const active = link.getAttribute('href') === `#${entry.target.id}`;

                link.classList.toggle('text-accent', active);
                link.classList.toggle('text-muted', ! active);

                if (active) {
                    link.setAttribute('aria-current', 'true');
                } else {
                    link.removeAttribute('aria-current');
                }
            });
        });
    }, { rootMargin: '-45% 0px -50% 0px' });

    sections.forEach((section) => observer.observe(section));
}

/**
 * Light/dark toggle. The initial class is set inline in the layout head so
 * there is no flash; this only handles the click and persistence.
 */
function initTheme() {
    const button = document.querySelector('[data-theme-toggle]');

    if (! button) {
        return;
    }

    button.addEventListener('click', () => {
        const dark = document.documentElement.classList.toggle('dark');

        try {
            localStorage.setItem('theme', dark ? 'dark' : 'light');
        } catch {
            // Private mode or blocked storage: the toggle still works for
            // this page view, it just will not be remembered.
        }

        button.setAttribute('aria-pressed', String(dark));
    });
}

function init() {
    initReveals();
    initTimeline();
    initNav();
    initTheme();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
