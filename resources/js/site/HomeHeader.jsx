/*
| The light home's top bar: name, two links, language, one action (email). It
| stays on a single line down to a phone, so the Services link folds away
| there (the hero has "See rates" instead).
*/

import { useLocale } from '../lib/i18n';
import { LanguageSwitch } from './Nav';
import { PageLink } from './PageLink';

export function HomeHeader({ person, email }) {
    const { locale, t } = useLocale();

    return (
        <header className="sticky top-0 z-50 border-b border-line bg-base/90 backdrop-blur">
            <div className="mx-auto flex h-16 max-w-6xl items-center gap-1 px-4 sm:gap-2 sm:px-6 lg:px-8">
                <PageLink to={`/${locale}`} translate="no" className="mr-auto whitespace-nowrap py-3 text-[15px] font-bold tracking-[-0.01em] sm:text-[16px]">
                    {person.short_name}
                </PageLink>

                <nav aria-label={t('Main')} className="flex items-center text-[15px]">
                    <a href="#services" className="press hidden rounded-md px-3 py-3 text-soft hover:text-ink md:block">
                        {t('Services')}
                    </a>
                    <PageLink to={`/${locale}/cv`} className="press rounded-md px-2 py-3 text-soft hover:text-ink sm:px-3">
                        {t('CV')}
                    </PageLink>
                </nav>

                <LanguageSwitch />

                <a href={email} className="btn-ink btn-ink-sm ml-1 sm:ml-2">
                    {t('Email me')}
                </a>
            </div>
        </header>
    );
}
