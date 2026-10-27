/*
| /{locale}/cv -- hero, pipelines (one per career path), the experience
| ledger, education/languages, case files, stack index and contact.
| Data: GET /api/{locale}/cv.
*/

import { Fragment, useEffect } from 'react';
import { Macro } from '../components/Macro';
import { prefetch } from '../lib/cache';
import { useLocale } from '../lib/i18n';
import { FlowBox, ProjectCells } from '../site/Blocks';
import { Contact, EmailButton } from '../site/Contact';
import { Nav } from '../site/Nav';
import { PageLink } from '../site/PageLink';
import { useDocumentMeta, usePage } from '../site/PublicLayout';

export function Cv() {
    const { locale } = useLocale();
    const data = usePage(`/${locale}/cv`);
    const { person, paths } = data;

    useDocumentMeta(data.meta);

    // The three path pages are the main places to go from here: load them
    // once the browser is idle so even a tap with no hover is instant.
    useEffect(() => {
        const warm = () => paths.forEach((path) => prefetch(`/${locale}/paths/${path.key}`));
        const idle = window.requestIdleCallback ? window.requestIdleCallback(warm) : window.setTimeout(warm, 1200);

        return () => (window.cancelIdleCallback ? window.cancelIdleCallback(idle) : window.clearTimeout(idle));
    }, [locale, paths]);

    return (
        <div>
            <Nav person={person} paths={paths} current="cv" />

            <main id="main" className="frame">
                <Hero person={person} />
                <Pipelines pipelines={data.pipelines} />
                <Ledger data={data} />
                {data.projects.length > 0 && <CaseFiles projects={data.projects} />}
                {data.skills.length > 0 && <StackIndex groups={data.skills} />}
            </main>

            <Contact person={person} />
        </div>
    );
}

/**
 * What the hero's data cells list. Proper nouns, so never translated.
 */
const focus = {
    SAP: 'ABAP, CDS views, OData v2/v4',
    Web: 'PHP, Laravel, PostgreSQL',
    Runtime: 'Docker, Nginx, Linux',
};

function Hero({ person }) {
    const { t } = useLocale();

    return (
        <section className="cells border-t-0">
            <div className="scanlines px-5 pb-12 pt-14 sm:px-8 lg:px-10 lg:pb-14 lg:pt-16">
                <Macro as="h1" text={t('Backend developer.')} fit max={150} />
            </div>

            <div className="grid grid-cols-[96px_1fr] gap-px bg-line sm:grid-cols-[150px_1fr] lg:grid-cols-[150px_1.3fr_1fr_1fr_auto]">
                {person.photo_url && (
                    <div className="halftone bg-base">
                        <img
                            src={person.photo_url}
                            alt={person.name}
                            width="150"
                            height="150"
                            fetchPriority="high"
                            className="block h-full min-h-24 w-full object-cover"
                        />
                    </div>
                )}

                <p className={`bg-base p-5 text-[15px] leading-[1.6] text-soft sm:p-7 sm:text-[16px] ${person.photo_url ? '' : 'col-span-2'}`}>
                    {t('I write the services between business rules and the database. SAP and PHP, in production since 2023.')}
                </p>

                <dl className="col-span-2 grid grid-cols-2 gap-px bg-line lg:col-span-2">
                    <div className="bg-base p-5 sm:p-7">
                        <dt className="label">{t('Status')}</dt>
                        <dd className="mt-1.5 flex items-center gap-2.5 text-[14px]">
                            <span className="size-2 shrink-0 bg-live" aria-hidden="true" />
                            {t('Open to backend roles')}
                        </dd>
                        <dt className="label mt-5">SAP</dt>
                        <dd className="mt-1.5 text-[14px]" translate="no">
                            {focus.SAP}
                        </dd>
                    </div>
                    <div className="bg-base p-5 sm:p-7">
                        <dt className="label">Web</dt>
                        <dd className="mt-1.5 text-[14px]" translate="no">
                            {focus.Web}
                        </dd>
                        <dt className="label mt-5">{t('Runtime')}</dt>
                        <dd className="mt-1.5 text-[14px]" translate="no">
                            {focus.Runtime}
                        </dd>
                    </div>
                </dl>

                <div className="col-span-2 flex items-end bg-base p-5 sm:p-7 lg:col-span-1">
                    <EmailButton email={person.email} />
                </div>
            </div>
        </section>
    );
}

/**
 * Each career path as the request path it is built on. A row links to the
 * path page; its red focus node is named so it travels to that page's flow.
 */
function Pipelines({ pipelines }) {
    const { locale, t } = useLocale();

    return (
        <section className="cells border-t-0 lg:grid-cols-[300px_1fr]">
            <div className="px-6 py-10 sm:px-7">
                <h2 className="macro text-[36px] leading-[0.92]">{t('Pipelines')}</h2>
                <p className="mt-5 max-w-[34ch] text-[14px] leading-[1.6] text-muted">
                    {t('The request paths I build and maintain, end to end.')}
                </p>
            </div>

            <div className="grid gap-px bg-line">
                {pipelines.map((pipeline) => (
                    <PageLink key={pipeline.key} to={`/${locale}/${pipeline.key}`} className="cell-link group block bg-base px-6 py-7 sm:px-8">
                        <span className="label flex items-center justify-between gap-4">
                            <span>{pipeline.title}</span>
                            <span className="text-signal transition-transform duration-200 group-hover:translate-x-1 motion-reduce:transform-none" aria-hidden="true">
                                &gt;&gt;&gt;
                            </span>
                        </span>
                        <span className="mt-4 flex flex-wrap items-center gap-x-3 gap-y-2" translate="no">
                            {pipeline.nodes.map((node, index) => (
                                <Fragment key={node.name}>
                                    {index > 0 && (
                                        <span className="text-[13px] text-signal" aria-hidden="true">
                                            ---&gt;
                                        </span>
                                    )}
                                    <FlowBox node={node} pathKey={pipeline.key} />
                                </Fragment>
                            ))}
                        </span>
                    </PageLink>
                ))}
            </div>
        </section>
    );
}

/**
 * Experience as dated entries rather than a timeline.
 */
function Ledger({ data }) {
    const { locale, t, choice } = useLocale();
    const { experience, educations, languages, paths } = data;
    const navFor = Object.fromEntries(paths.map((path) => [path.key, path.nav]));

    return (
        <section className="mt-24 lg:mt-28">
            <div className="mb-6 flex flex-wrap items-end justify-between gap-4">
                <h2 className="macro text-[clamp(3.5rem,8vw,6rem)]">{t('Ledger')}</h2>
                {experience.count > 0 && (
                    <p className="label">
                        {choice(':count entry|:count entries', experience.count)} / {experience.years}
                    </p>
                )}
            </div>

            <div className="cells">
                <div className="label hidden grid-cols-[110px_110px_1.1fr_1.6fr_1fr] bg-raised md:grid" aria-hidden="true">
                    <span className="px-4 py-4">{t('From')}</span>
                    <span className="px-4 py-4">{t('To')}</span>
                    <span className="px-4 py-4">{t('Company')}</span>
                    <span className="px-4 py-4">{t('Role')}</span>
                    <span className="px-4 py-4">{t('Paths')}</span>
                </div>

                <ol className="grid gap-px bg-line">
                    {experience.items.map((row) => (
                        <li key={row.id} className="grid grid-cols-2 bg-base text-[13.5px] md:grid-cols-[110px_110px_1.1fr_1.6fr_1fr] md:items-center">
                            <span className="px-4 pb-1 pt-4 text-muted md:py-5 md:text-ink">
                                <span className="sr-only">{t('From')} </span>
                                {row.from}
                            </span>
                            <span className="px-4 pb-1 pt-4 text-right text-muted md:py-5 md:text-left md:text-ink">
                                <span className="sr-only">{t('To')} </span>
                                {row.to ?? <span className="text-signal">{t('Present')}</span>}
                            </span>
                            <span className="col-span-2 px-4 pt-2 font-bold md:col-span-1 md:py-5">{row.company}</span>
                            <span className="col-span-2 px-4 pt-1 text-soft md:col-span-1 md:py-5 md:text-ink">{row.role}</span>
                            <span className="col-span-2 flex flex-wrap gap-x-3 px-4 pb-4 pt-2 md:col-span-1 md:py-5">
                                {row.paths.map((key) => (
                                    <PageLink key={key} to={`/${locale}/${key}`} className="press uppercase tracking-[0.06em] text-signal hover:text-ink">
                                        [ {navFor[key]} ]
                                    </PageLink>
                                ))}
                            </span>
                        </li>
                    ))}
                </ol>
            </div>

            {(educations.length > 0 || languages.length > 0) && (
                <div className="cells mt-6 md:grid-cols-2">
                    {educations.length > 0 && (
                        <div className="px-5 py-6 sm:px-6">
                            <h3 className="label">{t('Education')}</h3>
                            {educations.map((education) => (
                                <div key={education.id} className="mt-3">
                                    <p className="text-[14.5px] font-bold">{education.degree}</p>
                                    <p className="mt-1 text-[13.5px] text-muted">
                                        {education.institution} / {education.start_year} - {education.end_year ?? t('Present')}
                                    </p>
                                </div>
                            ))}
                        </div>
                    )}
                    {languages.length > 0 && (
                        <div className="px-5 py-6 sm:px-6">
                            <h3 className="label">{t('Languages')}</h3>
                            <dl className="mt-3 grid grid-cols-[auto_1fr] gap-x-6 gap-y-1.5 text-[14px]">
                                {languages.map((language) => (
                                    <Fragment key={language.id}>
                                        <dt className="font-bold">{language.name}</dt>
                                        <dd className="text-muted">{language.level_label}</dd>
                                    </Fragment>
                                ))}
                            </dl>
                        </div>
                    )}
                </div>
            )}
        </section>
    );
}

/**
 * Published projects. The first one leads; the rest stack beside it.
 */
function CaseFiles({ projects }) {
    const { t } = useLocale();

    return (
        <section className="mt-24 lg:mt-28">
            <h2 className="macro mb-6 text-[clamp(3.5rem,8vw,6rem)]">{t('Case files')}</h2>
            <ProjectCells projects={projects} />
        </section>
    );
}

/**
 * Skill groups from the admin, dense and mono.
 */
function StackIndex({ groups }) {
    const { t } = useLocale();

    // Up to five groups share one row; more wrap into balanced rows. The last
    // group takes whatever is left of its row, so no empty cell shows the line.
    const cols = groups.length <= 5 ? groups.length : Math.ceil(groups.length / 2);
    const tail = cols - (groups.length % cols || cols) + 1;

    return (
        <section className="cells mt-24 lg:mt-28 lg:grid-cols-[300px_1fr]">
            <div className="px-6 py-8 sm:px-7">
                <h2 className="macro text-[36px] leading-[0.92]">{t('Stack index')}</h2>
            </div>
            <div className="grid gap-px bg-line sm:grid-cols-2 xl:grid-cols-[repeat(var(--cols),minmax(0,1fr))]" style={{ '--cols': cols, '--tail': tail }}>
                {groups.map((group) => (
                    <div key={group.id} className="bg-base px-6 py-7 sm:last:odd:col-span-2 xl:last:col-[span_var(--tail)]">
                        <h3 className="label">{group.label}</h3>
                        <ul className="mt-3 text-[14px] leading-[2]" translate="no">
                            {group.skills.map((skill) => (
                                <li key={skill}>{skill}</li>
                            ))}
                        </ul>
                    </div>
                ))}
            </div>
        </section>
    );
}
