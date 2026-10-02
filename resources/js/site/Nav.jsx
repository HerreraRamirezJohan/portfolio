/*
| Shared public nav: three compartments of one ruled bar. `current` is the
| active path key, or null on the home page.
*/

import { useEffect, useRef, ViewTransition } from 'react';
import { useLocation } from 'react-router';
import { config } from '../lib/config';
import { useLocale } from '../lib/i18n';
import { PageLink } from './PageLink';

export function Nav({ person, paths, current }) {
    const { locale, t } = useLocale();

    // Named, so the bar holds still while the page underneath changes.
    return (
        <ViewTransition name="site-nav">
            <header className="sticky top-0 z-50 bg-base">
                <div className="frame">
                    <div className="cells grid-cols-[1fr_auto] border-t-0 md:grid-cols-[1.3fr_2fr_1fr]">
                        <PageLink
                            to={`/${locale}`}
                            className="flex h-16 items-center px-5 text-[13px] font-bold uppercase tracking-[0.1em]"
                        >
                            {person.short_name}
                        </PageLink>

                        <nav aria-label={t('Main')} className="hidden h-16 items-center gap-1 px-3 md:flex">
                            {paths.map((path) => (
                                <PageLink
                                    key={path.key}
                                    to={`/${locale}/${path.key}`}
                                    aria-current={current === path.key ? 'page' : undefined}
                                    className={`press px-3 py-3 text-[12.5px] uppercase tracking-[0.1em] ${
                                        current === path.key ? 'font-bold text-ink' : 'text-muted hover:text-ink'
                                    }`}
                                >
                                    {current === path.key ? `[ ${path.nav} ]` : path.nav}
                                </PageLink>
                            ))}
                            <a href="#contact" className="press px-3 py-3 text-[12.5px] uppercase tracking-[0.1em] text-muted hover:text-ink">
                                {t('Contact')}
                            </a>
                        </nav>

                        <div className="flex h-16 items-center justify-end gap-2 pl-3 pr-2 sm:pr-3">
                            <LanguageSwitch />
                            <MobileMenu paths={paths} current={current} />
                        </div>
                    </div>
                </div>
            </header>
        </ViewTransition>
    );
}

/**
 * The same page in every locale: swap the first path segment.
 */
function LanguageSwitch() {
    const { locale } = useLocale();
    const { pathname } = useLocation();
    const rest = pathname.split('/').slice(2).join('/');

    return (
        <div className="flex items-center text-[12.5px] tracking-[0.1em]">
            {config.locales.map((code, index) => (
                <span key={code} className="flex items-center">
                    {index > 0 && (
                        <span className="text-faint" aria-hidden="true">
                            /
                        </span>
                    )}
                    <PageLink
                        to={`/${code}${rest ? `/${rest}` : ''}`}
                        hrefLang={code}
                        lang={code}
                        aria-current={code === locale ? 'true' : undefined}
                        className={`press px-2 py-3 ${code === locale ? 'font-bold text-ink' : 'text-muted hover:text-ink'}`}
                    >
                        {code.toUpperCase()}
                    </PageLink>
                </span>
            ))}
        </div>
    );
}

/**
 * A <details> menu; closes when one of its links is used or Escape is
 * pressed, since an in-page link would otherwise leave it open.
 */
function MobileMenu({ paths, current }) {
    const { locale, t } = useLocale();
    const menu = useRef(null);

    useEffect(() => {
        function onKeyDown(event) {
            if (event.key === 'Escape' && menu.current?.open) {
                menu.current.open = false;
                menu.current.querySelector('summary')?.focus();
            }
        }

        document.addEventListener('keydown', onKeyDown);

        return () => document.removeEventListener('keydown', onKeyDown);
    }, []);

    function closeOnLink(event) {
        if (event.target.closest('a')) {
            menu.current.open = false;
        }
    }

    return (
        <details ref={menu} onClick={closeOnLink} className="group relative md:hidden">
            <summary className="press flex h-11 cursor-pointer list-none items-center border border-line-strong px-3 text-[12px] font-bold uppercase tracking-[0.1em]">
                <span className="group-open:hidden">{t('Menu')}</span>
                <span className="hidden group-open:inline" aria-hidden="true">
                    [ x ]
                </span>
            </summary>

            <nav aria-label={t('Main')} className="cells absolute right-0 top-[calc(100%+0.5rem)] w-64">
                {paths.map((path) => (
                    <PageLink
                        key={path.key}
                        to={`/${locale}/${path.key}`}
                        aria-current={current === path.key ? 'page' : undefined}
                        className="cell-link flex items-center justify-between px-4 py-4 text-[13px] uppercase tracking-[0.08em]"
                    >
                        {path.card_title}
                        <span className={current === path.key ? 'text-signal' : 'text-faint'} aria-hidden="true">
                            &gt;&gt;&gt;
                        </span>
                    </PageLink>
                ))}
                <a href="#contact" className="cell-link px-4 py-4 text-[13px] uppercase tracking-[0.08em]">
                    {t('Contact')}
                </a>
            </nav>
        </details>
    );
}
