/*
| Shared public nav. `current` is the active path key, or null on the home page.
*/

import { useEffect, useRef } from 'react';
import { Link, useLocation } from 'react-router';
import { Icon } from '../components/Icon';
import { ThemeToggle } from '../components/ThemeToggle';
import { config } from '../lib/config';
import { useLocale } from '../lib/i18n';

export function Nav({ person, paths, current }) {
    const { locale, t } = useLocale();

    return (
        <header className="sticky top-0 z-50 border-b border-line bg-base/85 backdrop-blur-md">
            <div className="mx-auto flex h-16 max-w-[1440px] items-center justify-between gap-4 px-5 sm:px-10 lg:h-20 lg:px-20">
                <Link to={`/${locale}`} className="flex items-center gap-2.5 text-[16px] font-semibold tracking-[-0.01em] sm:text-[17px]">
                    <span className="size-2.5 rounded-full bg-ink" aria-hidden="true" />
                    {person.short_name}
                </Link>

                <nav aria-label={t('Main')} className="hidden items-center gap-1 md:flex">
                    {paths.map((path) => (
                        <Link
                            key={path.key}
                            to={`/${locale}/${path.key}`}
                            aria-current={current === path.key ? 'page' : undefined}
                            className={`theme-${path.key} px-4 py-3 text-[15px] transition-colors ${
                                current === path.key
                                    ? 'font-semibold text-path underline decoration-2 underline-offset-[10px]'
                                    : 'font-medium text-muted hover:text-ink'
                            }`}
                        >
                            {path.nav}
                        </Link>
                    ))}
                    <a href="#contact" className="px-4 py-3 text-[15px] font-medium text-muted transition-colors hover:text-ink">
                        {t('Contact')}
                    </a>
                </nav>

                <div className="flex items-center gap-2 sm:gap-3">
                    <LanguageSwitch />
                    <ThemeToggle />
                    <MobileMenu paths={paths} current={current} />
                </div>
            </div>
        </header>
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
        <div className="flex rounded-full border border-line bg-surface p-[3px]">
            {config.locales.map((code) => (
                <Link
                    key={code}
                    to={`/${code}${rest ? `/${rest}` : ''}`}
                    hrefLang={code}
                    lang={code}
                    aria-current={code === locale ? 'true' : undefined}
                    className={`rounded-full px-3 py-2 font-mono text-xs font-medium transition-colors sm:px-3.5 sm:py-2.5 ${
                        code === locale ? 'bg-ink text-surface' : 'text-muted hover:text-ink'
                    }`}
                >
                    {code.toUpperCase()}
                </Link>
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
        <details ref={menu} onClick={closeOnLink} className="relative md:hidden">
            <summary className="flex size-11 cursor-pointer list-none items-center justify-center rounded-full border border-line bg-surface text-ink">
                <span className="sr-only">{t('Menu')}</span>
                <Icon name="menu" />
            </summary>

            <nav
                aria-label={t('Main')}
                className="absolute right-0 top-[calc(100%+0.75rem)] flex w-60 flex-col rounded-2xl border border-line bg-surface p-2 shadow-[0_12px_40px_rgb(0_0_0/0.08)]"
            >
                {paths.map((path) => (
                    <Link
                        key={path.key}
                        to={`/${locale}/${path.key}`}
                        aria-current={current === path.key ? 'page' : undefined}
                        className={`theme-${path.key} flex items-center gap-3 rounded-xl px-3 py-3 text-[15px] font-medium hover:bg-base`}
                    >
                        <span className="size-2 rounded-full bg-path" aria-hidden="true" />
                        {path.card_title}
                    </Link>
                ))}
                <a href="#contact" className="rounded-xl px-3 py-3 pl-8 text-[15px] font-medium hover:bg-base">
                    {t('Contact')}
                </a>
            </nav>
        </details>
    );
}
