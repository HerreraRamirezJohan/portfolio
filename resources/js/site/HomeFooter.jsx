/*
| The light home's footer: who, where else to find them, and the trademark
| and logo credits the site owes (see Credits in site/Contact.jsx).
*/

import { useLocale } from '../lib/i18n';
import { Credits, socials } from './Contact';
import { PageLink } from './PageLink';

export function HomeFooter({ person }) {
    const { locale, t } = useLocale();
    const links = socials(person, t);

    return (
        <footer className="border-t border-line">
            <div className="mx-auto max-w-6xl px-4 pb-8 pt-10 sm:px-6 lg:px-8">
                <div className="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                    <p className="text-[15px] text-muted">
                        © {new Date().getFullYear()} {person.name}
                    </p>

                    <nav aria-label={t('Footer')} className="-mx-3 flex flex-wrap items-center text-[15px]">
                        <PageLink to={`/${locale}/cv`} className="press rounded-md px-3 py-3 text-soft hover:text-ink">
                            {t('My CV')}
                        </PageLink>
                        {links.map(([label, url]) => (
                            <a key={label} href={url} rel="noopener noreferrer" target="_blank" className="press rounded-md px-3 py-3 text-soft hover:text-ink">
                                {label}
                            </a>
                        ))}
                    </nav>
                </div>

                <Credits />
            </div>
        </footer>
    );
}
