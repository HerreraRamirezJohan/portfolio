/*
| Wraps every /{locale}/... page: validates the locale segment, provides it
| to the tree, and keeps <html lang> and the scroll position in step with
| client-side navigation.
*/

import { useEffect } from 'react';
import { Outlet, useLocation, useParams } from 'react-router';
import { isLocale } from '../lib/config';
import { LocaleProvider, translate } from '../lib/i18n';
import { NotFound } from '../pages/NotFound';

export function PublicLayout() {
    const { locale } = useParams();
    const { pathname, hash } = useLocation();

    useEffect(() => {
        document.documentElement.lang = locale;
    }, [locale]);

    useEffect(() => {
        if (! hash) {
            window.scrollTo(0, 0);
        }
    }, [pathname, hash]);

    if (! isLocale(locale)) {
        return <NotFound />;
    }

    return (
        <LocaleProvider locale={locale}>
            <a
                href="#main"
                className="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-lg focus:bg-ink focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-surface"
            >
                {translate(locale, 'Skip to content')}
            </a>
            <Outlet />
        </LocaleProvider>
    );
}

/**
 * Sets document.title (and the description) from an API payload's `meta`.
 */
export function useDocumentMeta(meta) {
    useEffect(() => {
        if (! meta) {
            return;
        }

        document.title = meta.title;
        document.querySelector('meta[name="description"]')?.setAttribute('content', meta.description ?? '');
    }, [meta]);
}

/**
 * Shown while a page's data loads, or if it fails.
 */
export function PageStatus({ error }) {
    return (
        <div className="flex min-h-screen items-center justify-center px-5" aria-busy={! error}>
            {error && (
                <p className="font-mono text-[13px] text-faint" role="alert">
                    {error.response?.status ?? ''} {error.message}
                </p>
            )}
        </div>
    );
}
