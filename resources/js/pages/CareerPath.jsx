/*
| /{locale}/{path} -- one page per career path (sap, web, infra).
| Data: GET /api/{locale}/paths/{path}.
*/

import { Fragment } from 'react';
import { useParams } from 'react-router';
import { Macro } from '../components/Macro';
import { Reveal } from '../components/Reveal';
import { TechChips } from '../components/TechChip';
import { config } from '../lib/config';
import { useLocale } from '../lib/i18n';
import { FlowBox, ProjectFooter } from '../site/Blocks';
import { Contact } from '../site/Contact';
import { Nav } from '../site/Nav';
import { PageLink } from '../site/PageLink';
import { useDocumentMeta, usePage } from '../site/PublicLayout';
import { NotFound } from './NotFound';

export function CareerPath() {
    const { path: key } = useParams();

    if (! config.paths.includes(key)) {
        return <NotFound />;
    }

    return <CareerPathPage pathKey={key} />;
}

function CareerPathPage({ pathKey }) {
    const { locale } = useLocale();
    const data = usePage(`/${locale}/paths/${pathKey}`);
    const { person, paths, path } = data;

    useDocumentMeta(data.meta);

    return (
        <div>
            <Nav person={person} paths={paths} current={path.key} />

            <main id="main" className="frame">
                <Intro data={data} />
                <Services services={path.services} />
                <Flow flow={path.flow} pathKey={path.key} />
                <HowAndWhere data={data} />
                {data.projects.length > 0 && <Work projects={data.projects} />}
                <NextPath next={data.next} />
            </main>

            <Contact person={person} compact />
        </div>
    );
}

const sectionTitle = 'macro mb-6 text-[clamp(2.25rem,5vw,4rem)]';

function Intro({ data }) {
    const { locale, t } = useLocale();
    const { path, years, roles } = data;

    return (
        <section className="cells border-t-0 lg:grid-cols-[1fr_400px]">
            <div className="scanlines flex flex-col gap-8 px-5 pb-12 pt-6 sm:px-8 lg:px-10 lg:pb-14">
                <nav aria-label={t('Breadcrumb')} className="label flex items-center gap-3">
                    <PageLink to={`/${locale}/cv`} className="press py-3 hover:text-ink">
                        &lt;&lt; {t('CV')}
                    </PageLink>
                    <span className="text-faint" aria-hidden="true">
                        /
                    </span>
                    <span className="text-ink" aria-current="page">
                        {path.number} / {path.nav}
                    </span>
                </nav>
                <Macro as="h1" text={`${path.title}.`} fit max={120} />
                <p className="max-w-[56ch] text-[15.5px] leading-[1.65] text-soft sm:text-[16.5px]">{path.lead}</p>
            </div>

            <div className="flex flex-col">
                <dl className="grid gap-px bg-line">
                    {years && (
                        <>
                            <Fact label={t('Years')} value={years} />
                            <Fact label={t('Roles')} value={roles.length} />
                        </>
                    )}
                    {path.facts.map((fact) => (
                        <Fact key={fact.label} label={fact.label} value={fact.value} />
                    ))}
                </dl>
                <div className="border-t border-line px-6 py-6">
                    <p className="label sr-only">{t('Stack')}</p>
                    <TechChips stack={path.stack} />
                </div>
            </div>
        </section>
    );
}

function Fact({ label, value }) {
    return (
        <div className="flex items-baseline justify-between gap-4 bg-base px-6 py-4">
            <dt className="label">{label}</dt>
            <dd className="text-right text-[14px] font-bold">{value}</dd>
        </div>
    );
}

function Services({ services }) {
    const { t } = useLocale();

    return (
        <section className="mt-24">
            <h2 className={sectionTitle}>{t('What I do')}</h2>
            <div className="cells sm:grid-cols-2 lg:grid-cols-4">
                {services.map((service, index) => (
                    <Reveal key={service.title} index={index} className="flex flex-col gap-3 px-6 py-7">
                        <h3 className="text-[15px] font-bold uppercase tracking-[0.04em]">{service.title}</h3>
                        <p className="text-[14px] leading-[1.6] text-soft">{service.body}</p>
                    </Reveal>
                ))}
            </div>
        </section>
    );
}

function Flow({ flow, pathKey }) {
    const { group } = flow;
    const outside = group ? flow.nodes.slice(0, group.from) : flow.nodes;
    const inside = group ? flow.nodes.slice(group.from) : [];

    return (
        <section className="mt-24">
            <div className="cells lg:grid-cols-[300px_1fr]">
                <div className="px-6 py-8 sm:px-7">
                    <h2 className="macro text-[30px] leading-[0.95]">{flow.title}</h2>
                    <p className="label mt-4">{flow.caption}</p>
                </div>

                <div className="flex flex-col gap-8 px-6 py-8 sm:px-8">
                    <ol className="flex flex-col items-start gap-3 lg:flex-row lg:items-center" translate="no">
                        {outside.map((node, index) => (
                            <Fragment key={node.name}>
                                <FlowNode node={node} pathKey={pathKey} />
                                {(index < outside.length - 1 || inside.length > 0) && <FlowArrow />}
                            </Fragment>
                        ))}

                        {inside.length > 0 && (
                            <li className="flex flex-col gap-3 border border-dashed border-line-strong p-4">
                                <span className="label">{group.label}</span>
                                <ol className="flex flex-col items-start gap-3 lg:flex-row lg:items-center">
                                    {inside.map((node, index) => (
                                        <Fragment key={node.name}>
                                            <FlowNode node={node} pathKey={pathKey} />
                                            {index < inside.length - 1 && <FlowArrow />}
                                        </Fragment>
                                    ))}
                                </ol>
                            </li>
                        )}
                    </ol>

                    <div className="flex flex-wrap items-center gap-x-3 gap-y-2 text-[14px] text-soft">
                        {flow.tags.map((tag) => (
                            <span key={tag} className="border border-line-strong px-2.5 py-1.5 text-[12px] uppercase tracking-[0.06em] text-ink">
                                {tag}
                            </span>
                        ))}
                        <span className="sm:ml-2">{flow.note}</span>
                    </div>
                </div>
            </div>
        </section>
    );
}

function FlowNode({ node, pathKey }) {
    return (
        <li className="flex flex-col gap-1.5">
            <span className="label">{node.layer}</span>
            <FlowBox node={node} pathKey={pathKey} />
        </li>
    );
}

function FlowArrow() {
    return (
        <li aria-hidden="true" className="text-[13px] text-signal lg:pt-5">
            <span className="lg:hidden">|</span>
            <span className="hidden lg:inline">---&gt;</span>
        </li>
    );
}

function HowAndWhere({ data }) {
    const { t } = useLocale();
    const { path, roles } = data;

    return (
        <section className="mt-24">
            <div className="cells lg:grid-cols-[5fr_7fr]">
                <div className="flex flex-col gap-5 px-6 py-9 sm:px-8">
                    <h2 className="label">{t('How I work')}</h2>
                    <blockquote className="font-display text-[24px] font-bold leading-[1.2] tracking-[-0.02em] text-balance sm:text-[28px]">
                        “{path.quote}”
                    </blockquote>
                    <p className="text-[14.5px] leading-[1.65] text-muted">{path.quote_note}</p>
                </div>

                <div className="flex flex-col">
                    <h2 className="label border-b border-line px-6 py-4 sm:px-8">{t('Where')}</h2>
                    <ol className="grid gap-px bg-line">
                        {roles.map((row) => (
                            <TimelineRow key={row.id} when={row.range} title={row.company}>
                                <span className="text-ink">{row.role}.</span> {row.blurb}
                            </TimelineRow>
                        ))}
                        {path.extras.map((extra) => (
                            <TimelineRow key={extra.title} when={extra.when} title={extra.title}>
                                {extra.body}
                            </TimelineRow>
                        ))}
                    </ol>
                </div>
            </div>
        </section>
    );
}

function TimelineRow({ when, title, children }) {
    return (
        <li className="grid gap-x-6 gap-y-1.5 bg-base px-6 py-6 sm:grid-cols-[140px_minmax(0,1fr)] sm:px-8">
            <span className="text-[13px] text-muted">{when}</span>
            <div className="flex flex-col gap-1.5">
                <span className="text-[15px] font-bold uppercase tracking-[0.03em]">{title}</span>
                <span className="text-[14px] leading-[1.6] text-soft">{children}</span>
            </div>
        </li>
    );
}

function Work({ projects }) {
    const { t } = useLocale();

    return (
        <section id="work" className="mt-24">
            <h2 className={sectionTitle}>{t('Work')}</h2>
            <div className="cells md:grid-cols-2">
                {projects.map((project, index) => (
                    <Reveal as="article" key={project.id} index={index} className="flex flex-col gap-3 px-6 py-8 sm:px-8">
                        {project.year && <p className="label">{project.year}</p>}
                        <h3 className="text-[19px] font-bold uppercase tracking-[0.02em]">{project.title}</h3>
                        {project.summary && <p className="text-[14.5px] leading-[1.65] text-soft">{project.summary}</p>}
                        <ProjectFooter project={project} />
                    </Reveal>
                ))}
            </div>
        </section>
    );
}

function NextPath({ next }) {
    const { locale, t } = useLocale();

    return (
        <PageLink to={`/${locale}/${next.key}`} className="cells group mt-24 grid-cols-[1fr_auto]">
            <span className="cell-link flex flex-col gap-4 px-6 py-9 sm:px-9">
                <span className="label">
                    {t('Next path')} / {next.number}
                </span>
                <span className="macro text-[clamp(1.75rem,4.5vw,3.75rem)] leading-[0.95]">{next.title}</span>
            </span>
            <span className="flex w-20 items-center justify-center bg-signal text-[18px] font-bold text-on-signal transition-colors group-hover:bg-ink sm:w-32" aria-hidden="true">
                &gt;&gt;&gt;
            </span>
        </PageLink>
    );
}
