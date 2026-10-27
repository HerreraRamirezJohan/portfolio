/*
| Chrome around every admin screen. The admin UI is always in the app's
| default locale; the ES|EN tabs choose which translation a form edits.
*/

import { useEffect } from 'react';
import { Link, NavLink, Outlet, useLocation, useNavigate } from 'react-router';
import { config } from '../lib/config';
import { http } from '../lib/http';
import { LocaleProvider, translate } from '../lib/i18n';

const t = (key) => translate(config.defaultLocale, key);

const sections = [
    ['/admin', 'Dashboard'],
    ['/admin/profile', 'Profile'],
    ['/admin/experience', 'Experience'],
    ['/admin/education', 'Education'],
    ['/admin/skills', 'Technical Skills'],
    ['/admin/languages', 'Languages'],
    ['/admin/projects', 'Projects'],
    ['/admin/services', 'Services'],
    ['/admin/process', 'Process'],
    ['/admin/faq', 'FAQ'],
    ['/admin/costs', 'Cost factors'],
];

export function AdminLayout() {
    const navigate = useNavigate();
    const { pathname } = useLocation();
    const signedIn = pathname !== '/admin/login';

    useEffect(() => {
        document.documentElement.lang = config.defaultLocale;
        document.title = t('Admin');
    }, []);

    async function logout() {
        await http.post('/logout');
        navigate('/admin/login', { replace: true });
    }

    return (
        <LocaleProvider locale={config.defaultLocale}>
            <div className="min-h-screen bg-stone-100 text-stone-800 dark:bg-stone-950 dark:text-stone-200">
                {signedIn && (
                    <header className="border-b border-stone-200 bg-white dark:border-stone-800 dark:bg-stone-900">
                        <div className="mx-auto flex max-w-5xl flex-wrap items-center gap-x-6 gap-y-3 px-6 py-3">
                            <Link className="font-semibold text-stone-900 dark:text-stone-50" to="/admin">
                                {t('Admin')}
                            </Link>

                            <nav className="flex flex-wrap gap-x-4 gap-y-1 text-sm">
                                {sections.map(([to, label]) => (
                                    <NavLink
                                        key={to}
                                        to={to}
                                        end
                                        className={({ isActive }) =>
                                            `transition hover:text-stone-900 dark:hover:text-stone-100 ${
                                                isActive ? 'font-medium text-stone-900 dark:text-stone-100' : 'text-stone-500 dark:text-stone-400'
                                            }`
                                        }
                                    >
                                        {t(label)}
                                    </NavLink>
                                ))}
                            </nav>

                            <div className="ml-auto flex items-center gap-4 text-sm">
                                <a
                                    className="text-stone-500 underline underline-offset-2 hover:text-stone-900 dark:text-stone-400 dark:hover:text-stone-100"
                                    href={`/${config.defaultLocale}`}
                                    target="_blank"
                                    rel="noreferrer"
                                >
                                    {t('View site')}
                                </a>
                                <button className="text-stone-500 hover:text-stone-900 dark:text-stone-400 dark:hover:text-stone-100" type="button" onClick={logout}>
                                    {t('Log out')}
                                </button>
                            </div>
                        </div>
                    </header>
                )}

                <main className="mx-auto max-w-5xl px-6 py-10">
                    <Outlet />
                </main>
            </div>
        </LocaleProvider>
    );
}
