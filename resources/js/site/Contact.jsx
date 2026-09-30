/*
| The dark contact band that closes every page. `compact` shrinks it to a
| footer on path pages.
*/

import { Reveal } from '../components/Reveal';
import { useLocale } from '../lib/i18n';

function socials(person, t) {
    return [
        ['GitHub', person.github_url],
        ['LinkedIn', person.linkedin_url],
        [t('Website'), person.website_url],
    ].filter(([, url]) => url);
}

function SocialLink({ label, href, external = true }) {
    return (
        <a
            href={href}
            {...(external ? { rel: 'noopener noreferrer', target: '_blank' } : {})}
            className="px-3.5 py-3 text-[15px] text-invert-soft transition-colors hover:text-invert-ink"
        >
            {label}
        </a>
    );
}

export function Contact({ person, compact = false }) {
    const { t } = useLocale();
    const links = socials(person, t);

    if (compact) {
        return (
            <footer id="contact" className="bg-invert text-invert-ink">
                <div className="mx-auto flex max-w-[1440px] flex-col gap-8 px-5 py-14 sm:px-10 md:flex-row md:items-center md:justify-between lg:px-20">
                    <div className="flex flex-col gap-3">
                        <p className="text-2xl font-semibold tracking-[-0.02em] sm:text-[28px]">{t('Have a process that needs a system?')}</p>
                        <a
                            href={`mailto:${person.email}`}
                            className="self-start text-lg text-invert-soft underline decoration-1 underline-offset-[6px] transition-colors hover:text-invert-ink"
                        >
                            {person.email}
                        </a>
                    </div>

                    <div className="-ml-3.5 flex flex-wrap md:ml-0">
                        {links.map(([label, url]) => (
                            <SocialLink key={label} label={label} href={url} />
                        ))}
                    </div>
                </div>
            </footer>
        );
    }

    return (
        <footer id="contact" className="bg-invert text-invert-ink">
            <div className="mx-auto flex max-w-[1440px] flex-col gap-16 px-5 pb-10 pt-20 sm:px-10 lg:px-20 lg:pt-32">
                <Reveal className="flex flex-col gap-6 lg:gap-7">
                    <p className="font-mono text-xs tracking-[0.06em] text-invert-muted">{t('Contact').toLocaleUpperCase()}</p>
                    <h2 className="max-w-[900px] text-[clamp(2.25rem,5vw,4rem)] font-semibold leading-[1.04] tracking-[-0.04em] text-balance">
                        {t('Have a process that needs a system?')}
                    </h2>
                    <a
                        href={`mailto:${person.email}`}
                        className="self-start break-all text-lg tracking-[-0.01em] underline decoration-1 underline-offset-8 transition-colors hover:text-invert-soft sm:text-[28px]"
                    >
                        {person.email}
                    </a>
                </Reveal>

                <div className="flex flex-col gap-4 border-t border-invert-line pt-6 sm:flex-row sm:items-center sm:justify-between">
                    <p className="font-mono text-[13px] text-invert-muted">
                        © {new Date().getFullYear()} {person.short_name}
                        {person.location && ` · ${person.location}`}
                    </p>

                    <div className="-ml-3.5 flex flex-wrap sm:ml-0">
                        {links.map(([label, url]) => (
                            <SocialLink key={label} label={label} href={url} />
                        ))}
                        {person.phone && <SocialLink label={person.phone} href={person.phone_href} external={false} />}
                    </div>
                </div>
            </div>
        </footer>
    );
}
