import { Link } from 'react-router';
import { config } from '../lib/config';
import { translate } from '../lib/i18n';

/**
 * Client-side fallback only: the server already 404s any URL outside the
 * routes in routes/web.php, so this is reached just by in-app navigation.
 */
export function NotFound() {
    const locale = config.defaultLocale;

    return (
        <main className="flex min-h-screen flex-col items-center justify-center gap-4 px-5 text-center">
            <p className="font-mono text-[13px] text-faint">404</p>
            <Link to={`/${locale}`} className="text-[15px] font-medium underline underline-offset-4">
                {translate(locale, 'Back to home')}
            </Link>
        </main>
    );
}
