/*
| The contact block and footer that close every page. `compact` drops the
| macro heading on path pages and keeps the action and the footer.
*/

import { Macro } from '../components/Macro';
import { Reveal } from '../components/Reveal';
import { useLocale } from '../lib/i18n';

function socials(person, t) {
    return [
        ['GitHub', person.github_url],
        ['LinkedIn', person.linkedin_url],
        [t('Website'), person.website_url],
    ].filter(([, url]) => url);
}

/**
 * Attribution for the PHP logo (CC BY-SA 4.0) and Tux, and the trademark
 * lines SAP and the Linux Foundation ask for.
 */
function Credits() {
    const { t } = useLocale();

    return (
        <p className="max-w-[760px] py-5 text-[11px] leading-[1.6] text-muted">
            {t(
                'PHP logo by Colin Viebrock, CC BY-SA 4.0. Tux by Larry Ewing and The GIMP. SAP and ABAP are trademarks or registered trademarks of SAP SE or its affiliates in Germany and in other countries. Linux is the registered trademark of Linus Torvalds.',
            )}
        </p>
    );
}

export function EmailButton({ email }) {
    const { t } = useLocale();

    return (
        <a href={`mailto:${email}`} className="btn-signal press">
            [ {t('Email me')} ]
            <span aria-hidden="true" className="tracking-normal">
                &gt;&gt;&gt;
            </span>
        </a>
    );
}

function Footer({ person }) {
    const { t } = useLocale();
    const links = socials(person, t);

    return (
        <div className="cells label mt-16 grid-cols-1 sm:grid-cols-[1fr_auto]">
            <p className="px-4 py-4">
                © {new Date().getFullYear()} {person.name}
            </p>
            <div className="flex flex-wrap gap-px bg-line">
                {links.map(([label, url]) => (
                    <a key={label} href={url} rel="noopener noreferrer" target="_blank" className="press bg-base px-4 py-4 hover:text-ink">
                        {label}
                    </a>
                ))}
                {person.phone && (
                    <a href={person.phone_href} className="press bg-base px-4 py-4 hover:text-ink">
                        {person.phone}
                    </a>
                )}
            </div>
        </div>
    );
}

export function Contact({ person, compact = false }) {
    const { t } = useLocale();

    return (
        <footer id="contact" className="frame pb-6">
            {compact ? (
                <div className="cells mt-24 grid-cols-1 md:grid-cols-[1fr_auto]">
                    <div className="flex flex-col gap-3 px-6 py-8">
                        <p className="macro text-[28px] sm:text-[36px]">{t('Send me the spec.')}</p>
                        <p className="text-[13.5px] text-muted">{person.email}</p>
                    </div>
                    <div className="flex items-center px-6 py-8">
                        <EmailButton email={person.email} />
                    </div>
                </div>
            ) : (
                <Reveal className="mt-24 grid gap-10 border-t-2 border-signal pt-12 lg:grid-cols-[1fr_420px] lg:items-end">
                    <Macro text={t('Send me the spec.')} fit max={150} />
                    <div className="flex flex-col items-start gap-5">
                        <p className="text-[15px] leading-[1.6] text-soft">
                            {t('I read every functional spec end to end before estimating. Yours included.')}
                        </p>
                        <EmailButton email={person.email} />
                        <a href={`mailto:${person.email}`} className="break-all text-[13.5px] text-muted hover:text-ink">
                            {person.email}
                        </a>
                    </div>
                </Reveal>
            )}

            <Footer person={person} />
            <Credits />
        </footer>
    );
}
