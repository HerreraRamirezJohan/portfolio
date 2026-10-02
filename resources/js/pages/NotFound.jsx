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
        <main className="site flex min-h-screen flex-col items-start justify-center gap-6 px-6 sm:px-12">
            <p className="macro text-[clamp(5rem,20vw,14rem)]">
                404<span className="text-signal">.</span>
            </p>
            <Link to={`/${locale}`} className="btn-signal press">
                &lt;&lt; {translate(locale, 'Back to home')}
            </Link>
        </main>
    );
}
