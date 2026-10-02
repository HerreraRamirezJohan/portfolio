/*
| Wraps every /{locale}/... page: validates the locale segment, provides it
| to the tree, keeps <html lang> and the scroll position in step with
| client-side navigation, and animates the change of page.
|
| Pages read their data with usePage(), which suspends. React Router
| navigates inside a transition, so the page on screen stays until the next
| one has its data, and <ViewTransition> then animates between the two.
*/

import { Component, Suspense, use, useEffect, useLayoutEffect, useMemo, ViewTransition } from 'react';
import { Outlet, useLocation, useParams } from 'react-router';
import { resource } from '../lib/cache';
import { isLocale } from '../lib/config';
import { LocaleProvider, translate } from '../lib/i18n';
import { NotFound } from '../pages/NotFound';

export function PublicLayout() {
    const { locale } = useParams();
    const { pathname, hash } = useLocation();

    useEffect(() => {
        document.documentElement.lang = locale;
    }, [locale]);

    // Layout effect: the view transition snapshots the new page right after
    // commit, and it must already be scrolled to the top by then.
    useLayoutEffect(() => {
        if (! hash) {
            window.scrollTo({ top: 0, behavior: 'instant' });
        }
    }, [pathname, hash]);

    if (! isLocale(locale)) {
        return <NotFound />;
    }

    return (
        <LocaleProvider locale={locale}>
            {/* `.site` scopes the Ledger palette and type; the admin has its own. */}
            <div className="site min-h-screen">
                <a
                    href="#main"
                    className="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:bg-signal focus:px-4 focus:py-3 focus:text-[13px] focus:font-bold focus:uppercase focus:tracking-[0.1em] focus:text-on-signal"
                >
                    {translate(locale, 'Skip to content')}
                </a>
                <ViewTransition default="page">
                    <PageErrors resetKey={pathname}>
                        <Suspense fallback={<PageStatus />}>
                            <Outlet />
                        </Suspense>
                    </PageErrors>
                </ViewTransition>
            </div>
        </LocaleProvider>
    );
}

/**
 * A public page's API payload. Suspends until it is loaded; a failed request
 * is thrown to <PageErrors>.
 */
export function usePage(url) {
    return use(useMemo(() => resource(url), [url]));
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
                <p className="label" role="alert">
                    {error.response?.status ?? ''} {error.message}
                </p>
            )}
        </div>
    );
}

/**
 * Shows a failed page load instead of unmounting the app; navigating away
 * clears it.
 */
class PageErrors extends Component {
    state = { error: null };

    static getDerivedStateFromError(error) {
        return { error };
    }

    componentDidUpdate(previous) {
        if (this.state.error && previous.resetKey !== this.props.resetKey) {
            this.setState({ error: null });
        }
    }

    render() {
        return this.state.error ? <PageStatus error={this.state.error} /> : this.props.children;
    }
}
