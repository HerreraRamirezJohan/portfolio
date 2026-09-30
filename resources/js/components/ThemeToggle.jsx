/*
| Light/dark toggle. The initial class is set inline in the shell's <head>
| so there is no flash; this only handles the click and persistence.
*/

import { useState } from 'react';
import { useLocale } from '../lib/i18n';
import { Icon } from './Icon';

export function ThemeToggle() {
    const { t } = useLocale();
    const [dark, setDark] = useState(() => document.documentElement.classList.contains('dark'));

    function toggle() {
        const next = document.documentElement.classList.toggle('dark');

        try {
            localStorage.setItem('theme', next ? 'dark' : 'light');
        } catch {
            // Private mode or blocked storage: the toggle still works for
            // this page view, it just will not be remembered.
        }

        setDark(next);
    }

    return (
        <button
            type="button"
            onClick={toggle}
            aria-pressed={dark}
            aria-label={t('Toggle theme')}
            className="flex size-11 items-center justify-center rounded-full border border-line bg-surface text-ink transition-colors hover:border-line-strong"
        >
            <Icon name={dark ? 'sun' : 'moon'} />
        </button>
    );
}
