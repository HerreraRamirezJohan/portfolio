/*
| /{locale} -- hero, the three path cards, a compact experience list,
| education/languages and contact. Data: GET /api/{locale}/portfolio.
*/

import { Link } from 'react-router';
import { Icon } from '../components/Icon';
import { Reveal } from '../components/Reveal';
import { Trans, useLocale } from '../lib/i18n';
import { useApi } from '../lib/useApi';
import { Contact } from '../site/Contact';
import { Nav } from '../site/Nav';
import { PageStatus, useDocumentMeta } from '../site/PublicLayout';

export function Portfolio() {
    const { locale } = useLocale();
    const { data, error } = useApi(`/${locale}/portfolio`);

    useDocumentMeta(data?.meta);

    if (! data) {
        return <PageStatus error={error} />;
    }

    const { person, paths } = data;

    return (
        <div>
            <Nav person={person} paths={paths} current={null} />

            <main id="main">
                <Hero person={person} />
                <Paths paths={paths} />
                <Experience data={data} />
            </main>

            <Contact person={person} />
        </div>
    );
}

function PathWord({ path, children }) {
    return <span className={`theme-${path} text-path`}>{children}</span>;
}

function Hero({ person }) {
    const { t } = useLocale();

    const socials = [
        ['GitHub', person.github_url],
        ['LinkedIn', person.linkedin_url],
    ].filter(([, url]) => url);

    return (
        <section className="mx-auto flex max-w-[1440px] flex-col gap-8 px-5 pb-20 pt-16 sm:px-10 sm:pt-24 lg:gap-10 lg:px-20 lg:pb-32 lg:pt-36">
            <div className="flex items-center gap-4">
                {person.photo_url && (
                    <img
                        src={person.photo_url}
                        alt={person.name}
                        width="56"
                        height="56"
                        className="size-12 overflow-hidden rounded-full bg-line object-cover text-[0px] sm:size-14"
                    />
                )}
                <div className="flex flex-col gap-1">
                    <p className="text-[15px] font-semibold sm:text-[16px]">{person.name}</p>
                    <p className="font-mono text-[11px] tracking-[0.06em] text-faint sm:text-xs">
                        {[person.headline, person.location].filter(Boolean).join(' · ').toLocaleUpperCase()}
                    </p>
                </div>
            </div>

            <h1 className="max-w-[1120px] text-[clamp(2.625rem,6.4vw,5.25rem)] font-semibold leading-[1.02] tracking-[-0.045em] text-balance">
                <Trans
                    k="I build across :sap, :web and :servers they run on."
                    values={{
                        sap: <PathWord path="sap">SAP</PathWord>,
                        web: <PathWord path="web">{t('the web')}</PathWord>,
                        servers: <PathWord path="infra">{t('the servers')}</PathWord>,
                    }}
                />
            </h1>

            <p className="max-w-[560px] text-lg leading-[1.5] text-muted sm:text-[21px]">
                {t('Full stack developer. Process first, screen second.')}
            </p>

            <div className="flex flex-col gap-2.5 sm:flex-row sm:items-center sm:gap-3">
                <a
                    href={`mailto:${person.email}`}
                    className="flex h-13 items-center justify-center gap-2.5 rounded-full bg-ink px-6 text-[16px] font-medium text-surface transition-opacity hover:opacity-85"
                >
                    {t('Get in touch')}
                    <Icon name="arrow-right" stroke={1.8} />
                </a>
                <div className="grid grid-cols-2 gap-2.5 sm:flex sm:gap-3">
                    {socials.map(([label, url]) => (
                        <a
                            key={label}
                            href={url}
                            rel="noopener noreferrer"
                            target="_blank"
                            className="flex h-13 items-center justify-center rounded-full border border-line-strong bg-surface px-5.5 text-[16px] font-medium transition-colors hover:border-ink"
                        >
                            {label}
                        </a>
                    ))}
                </div>
            </div>
        </section>
    );
}

function Paths({ paths }) {
    const { locale, t } = useLocale();

    return (
        <section className="mx-auto flex max-w-[1440px] flex-col gap-6 px-5 pb-20 sm:px-10 lg:gap-8 lg:px-20 lg:pb-34">
            <div className="flex items-end justify-between gap-4">
                <h2 className="text-[28px] font-semibold leading-[1.1] tracking-[-0.03em] lg:text-[40px]">{t('Three paths')}</h2>
                <p className="hidden font-mono text-[13px] text-faint sm:block">{t('Pick one to go deeper')}</p>
            </div>

            <div className="grid gap-4 md:grid-cols-3 lg:gap-5">
                {paths.map((path, index) => (
                    <Reveal
                        as={Link}
                        key={path.key}
                        index={index}
                        to={`/${locale}/${path.key}`}
                        className={`theme-${path.key} lift flex flex-col gap-5 rounded-[20px] border border-line bg-surface p-6 lg:min-h-[420px] lg:gap-7 lg:p-8`}
                    >
                        <div className="flex items-center justify-between">
                            <span className="flex size-11 items-center justify-center rounded-xl bg-path-soft text-path lg:size-12">
                                <Icon name={path.key} size={24} stroke={1.6} />
                            </span>
                            <span className="font-mono text-xs text-faint lg:text-[13px]">{path.number}</span>
                        </div>

                        <div className="flex flex-col gap-2 lg:grow lg:gap-3">
                            <h3 className="text-[22px] font-semibold leading-[1.15] tracking-[-0.02em] lg:text-[28px]">{path.card_title}</h3>
                            <p className="text-[15px] leading-[1.55] text-muted lg:text-[16px]">{path.card}</p>
                        </div>

                        <div className="hidden flex-wrap gap-2 lg:flex">
                            {path.highlights.map((tech) => (
                                <span key={tech} className="rounded-full border border-line px-2.5 py-1.5 font-mono text-xs text-chip">
                                    {tech}
                                </span>
                            ))}
                        </div>

                        <div className="flex items-center justify-between lg:border-t lg:border-line lg:pt-5">
                            <span className="font-mono text-[13px] text-faint">{path.years}</span>
                            <span className="flex items-center gap-2 text-[15px] font-semibold text-path">
                                {t('Explore')}
                                <Icon name="arrow-right" stroke={1.8} />
                            </span>
                        </div>
                    </Reveal>
                ))}
            </div>
        </section>
    );
}

function Experience({ data }) {
    const { locale, t, choice } = useLocale();
    const { experience, educations, languages, paths } = data;
    const navFor = Object.fromEntries(paths.map((path) => [path.key, path.nav]));

    return (
        <section className="border-t border-line">
            <div className="mx-auto grid max-w-[1440px] gap-8 px-5 py-16 sm:px-10 lg:grid-cols-12 lg:gap-x-6 lg:px-20 lg:pb-32 lg:pt-28">
                <div className="flex flex-col gap-3 lg:col-span-4">
                    <h2 className="text-[28px] font-semibold leading-[1.1] tracking-[-0.03em] lg:text-[40px]">{t('Experience')}</h2>
                    {experience.count > 0 && (
                        <p className="font-mono text-[13px] text-faint">
                            {choice(':count role|:count roles', experience.count)} · {experience.years}
                        </p>
                    )}
                </div>

                <div className="flex flex-col lg:col-span-8">
                    <ol className="border-b border-line">
                        {experience.items.map((row) => (
                            <li
                                key={row.id}
                                className="grid gap-x-6 gap-y-1.5 border-t border-line py-5 sm:grid-cols-[170px_minmax(0,1fr)_auto] sm:items-center lg:py-6"
                            >
                                <div className="flex items-center justify-between sm:contents">
                                    <span className="font-mono text-xs text-faint sm:text-[13px]">{row.range}</span>
                                    <div className="flex gap-1.5 sm:order-last">
                                        {row.paths.map((key) => (
                                            <Link
                                                key={key}
                                                to={`/${locale}/${key}`}
                                                className={`theme-${key} rounded-full bg-path-soft px-2.5 py-1 font-mono text-[11px] font-medium text-path sm:text-xs`}
                                            >
                                                {navFor[key]}
                                            </Link>
                                        ))}
                                    </div>
                                </div>
                                <div className="flex flex-col gap-1">
                                    <span className="text-[17px] font-semibold tracking-[-0.01em] lg:text-lg">{row.role}</span>
                                    <span className="text-sm text-muted lg:text-[15px]">{row.company}</span>
                                </div>
                            </li>
                        ))}
                    </ol>

                    <div className="mt-10 grid gap-8 sm:grid-cols-2 sm:gap-6 lg:mt-12">
                        {educations.length > 0 && (
                            <div className="flex flex-col gap-2">
                                <p className="font-mono text-xs tracking-[0.06em] text-faint">{t('Education').toLocaleUpperCase()}</p>
                                {educations.map((education) => (
                                    <div key={education.id} className="flex flex-col gap-2">
                                        <p className="text-[16px] font-semibold">{education.degree}</p>
                                        <p className="text-[15px] text-muted">
                                            {education.institution} · {education.start_year} — {education.end_year ?? t('Present')}
                                        </p>
                                    </div>
                                ))}
                            </div>
                        )}

                        {languages.length > 0 && (
                            <div className="flex flex-col gap-2">
                                <p className="font-mono text-xs tracking-[0.06em] text-faint">{t('Languages').toLocaleUpperCase()}</p>
                                {languages.map((language, index) => (
                                    <p key={language.id} className={index === 0 ? 'text-[16px] font-semibold' : 'text-[15px] text-muted'}>
                                        {language.name} — {language.level_label}
                                    </p>
                                ))}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </section>
    );
}
