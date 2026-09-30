/*
| /{locale}/{path} -- one page per career path (sap, web, infra).
| Data: GET /api/{locale}/paths/{path}.
*/

import { Fragment } from 'react';
import { Link, useParams } from 'react-router';
import { Icon } from '../components/Icon';
import { Reveal } from '../components/Reveal';
import { TechChips } from '../components/TechChip';
import { config } from '../lib/config';
import { useLocale } from '../lib/i18n';
import { useApi } from '../lib/useApi';
import { Contact } from '../site/Contact';
import { Nav } from '../site/Nav';
import { PageStatus, useDocumentMeta } from '../site/PublicLayout';
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
    const { data, error } = useApi(`/${locale}/paths/${pathKey}`);

    useDocumentMeta(data?.meta);

    // Also wait out a stale payload from the previous path while the next loads.
    if (! data || data.path.key !== pathKey) {
        return <PageStatus error={error} />;
    }

    const { person, paths, path } = data;

    return (
        <div className={`theme-${path.key}`}>
            <Nav person={person} paths={paths} current={path.key} />

            <main id="main">
                <Intro data={data} />
                <Services services={path.services} />
                <Flow flow={path.flow} />
                <HowAndWhere data={data} />
                {data.projects.length > 0 && <Work projects={data.projects} />}
                <NextPath next={data.next} />
            </main>

            <Contact person={person} compact />
        </div>
    );
}

const sectionTitle = 'text-[26px] font-semibold leading-[1.15] tracking-[-0.025em] lg:text-[32px]';

function Intro({ data }) {
    const { locale, t } = useLocale();
    const { path, years, roles } = data;

    return (
        <section className="mx-auto flex max-w-[1440px] flex-col gap-10 px-5 pb-20 pt-8 sm:px-10 lg:gap-16 lg:px-20 lg:pb-30 lg:pt-16">
            <nav aria-label={t('Breadcrumb')} className="flex items-center gap-3 font-mono text-[13px] text-faint">
                <Link to={`/${locale}`} className="flex items-center gap-2 py-3 text-muted transition-colors hover:text-ink">
                    <Icon name="arrow-left" size={16} stroke={1.8} />
                    {t('All paths')}
                </Link>
                <span aria-hidden="true">/</span>
                <span className="text-ink" aria-current="page">
                    {path.number} · {path.nav}
                </span>
            </nav>

            <div className="grid gap-10 lg:grid-cols-12 lg:items-start lg:gap-x-6">
                <div className="flex flex-col gap-6 lg:col-span-7 lg:gap-7">
                    <span className="flex items-center gap-2 self-start rounded-full bg-path-soft px-3 py-1.5 font-mono text-xs font-medium tracking-[0.04em] text-path">
                        <span className="size-1.5 rounded-full bg-path" aria-hidden="true" />
                        {t('Path').toLocaleUpperCase()} {path.number}
                    </span>
                    <h1 className="text-[clamp(2.625rem,6vw,5rem)] font-semibold leading-none tracking-[-0.045em] text-balance">{path.title}</h1>
                    <p className="max-w-[560px] text-lg leading-[1.55] text-muted sm:text-xl">{path.lead}</p>
                </div>

                <dl className="flex flex-col gap-5 rounded-[20px] border border-line bg-surface p-6 sm:p-8 lg:col-span-4 lg:col-start-9">
                    {years && (
                        <>
                            <Fact label={t('Years')} value={years} />
                            <Fact label={t('Roles')} value={roles.length} />
                        </>
                    )}
                    {path.facts.map((fact) => (
                        <Fact key={fact.label} label={fact.label} value={fact.value} />
                    ))}
                    <div className="border-t border-line pt-5">
                        <dt className="sr-only">{t('Stack')}</dt>
                        <dd>
                            <TechChips stack={path.stack} />
                        </dd>
                    </div>
                </dl>
            </div>
        </section>
    );
}

function Fact({ label, value }) {
    return (
        <div className="flex justify-between gap-4">
            <dt className="font-mono text-[13px] text-faint">{label}</dt>
            <dd className="text-right text-[15px] font-semibold">{value}</dd>
        </div>
    );
}

function Services({ services }) {
    const { t } = useLocale();

    return (
        <section className="mx-auto flex max-w-[1440px] flex-col gap-8 px-5 pb-20 sm:px-10 lg:px-20 lg:pb-30">
            <h2 className={sectionTitle}>{t('What I do')}</h2>
            <div className="grid gap-8 sm:grid-cols-2 sm:gap-6 lg:grid-cols-4">
                {services.map((service, index) => (
                    <Reveal
                        key={service.title}
                        index={index}
                        className={`flex flex-col gap-2.5 pt-6 ${index === 0 ? 'border-t-2 border-path' : 'border-t border-line-strong'}`}
                    >
                        <span className="font-mono text-xs text-path">{String(index + 1).padStart(2, '0')}</span>
                        <h3 className="text-xl font-semibold tracking-[-0.015em]">{service.title}</h3>
                        <p className="text-[15px] leading-[1.55] text-muted">{service.body}</p>
                    </Reveal>
                ))}
            </div>
        </section>
    );
}

function Flow({ flow }) {
    const { dark, group } = flow;
    const outside = group ? flow.nodes.slice(0, group.from) : flow.nodes;
    const inside = group ? flow.nodes.slice(group.from) : [];

    return (
        <section className="mx-auto max-w-[1440px] px-5 pb-20 sm:px-10 lg:px-20 lg:pb-30">
            <Reveal
                className={`flex flex-col gap-8 rounded-3xl p-6 sm:p-10 lg:gap-10 lg:p-12 ${
                    dark ? 'bg-invert text-invert-ink' : 'border border-line bg-surface'
                }`}
            >
                <div className="flex flex-wrap items-end justify-between gap-2">
                    <h2 className={sectionTitle}>{flow.title}</h2>
                    <p className={`font-mono text-[13px] ${dark ? 'text-invert-muted' : 'text-faint'}`}>{flow.caption}</p>
                </div>

                <ol className="flex flex-col items-stretch gap-3 lg:flex-row lg:items-center lg:gap-4">
                    {outside.map((node, index) => (
                        <Fragment key={node.name}>
                            <FlowNode node={node} dark={dark} />
                            {(index < outside.length - 1 || inside.length > 0) && <FlowArrow dark={dark} />}
                        </Fragment>
                    ))}

                    {inside.length > 0 && (
                        <li
                            className={`flex flex-col gap-4 rounded-[20px] border border-dashed p-4 lg:flex-[3] lg:p-5 ${
                                dark ? 'border-invert-muted/50' : 'border-line-strong'
                            }`}
                        >
                            <span className={`font-mono text-xs ${dark ? 'text-invert-muted' : 'text-faint'}`}>{group.label}</span>
                            <ol className="flex flex-col items-stretch gap-3 lg:flex-row lg:items-center lg:gap-4">
                                {inside.map((node, index) => (
                                    <Fragment key={node.name}>
                                        <FlowNode node={node} dark={dark} />
                                        {index < inside.length - 1 && <FlowArrow dark={dark} />}
                                    </Fragment>
                                ))}
                            </ol>
                        </li>
                    )}
                </ol>

                <div className={`flex flex-wrap items-center gap-2 text-[15px] ${dark ? 'text-invert-soft' : 'text-muted'}`}>
                    {flow.tags.map((tag) => (
                        <span
                            key={tag}
                            className={`rounded-full px-2.5 py-1.5 font-mono text-xs font-medium ${
                                dark ? 'border border-invert-line text-invert-ink' : 'bg-path-soft text-path'
                            }`}
                        >
                            {tag}
                        </span>
                    ))}
                    <span className="sm:ml-2">{flow.note}</span>
                </div>
            </Reveal>
        </section>
    );
}

function FlowNode({ node, dark }) {
    const box = node.focus ? 'border-path bg-path text-on-path' : dark ? 'border-invert-line bg-invert-raised' : 'border-line bg-base';
    const layer = node.focus ? 'text-on-path' : dark ? 'text-invert-muted' : 'text-faint';

    return (
        <li className={`flex flex-col gap-2 rounded-2xl border p-5 lg:flex-1 lg:p-6 ${box}`}>
            <span className={`font-mono text-[11px] tracking-[0.08em] ${layer}`}>{node.layer.toLocaleUpperCase()}</span>
            <span className="text-lg font-semibold tracking-[-0.015em] lg:text-xl">{node.name}</span>
        </li>
    );
}

function FlowArrow({ dark }) {
    return (
        <li aria-hidden="true" className={`flex justify-center ${dark ? 'text-invert-muted' : 'text-faint'}`}>
            <Icon name="arrow-right" size={26} stroke={1.6} className="rotate-90 lg:rotate-0" />
        </li>
    );
}

function HowAndWhere({ data }) {
    const { t } = useLocale();
    const { path, roles } = data;

    return (
        <section className="mx-auto grid max-w-[1440px] gap-12 px-5 pb-20 sm:px-10 lg:grid-cols-12 lg:gap-x-6 lg:px-20 lg:pb-30">
            <div className="flex flex-col gap-5 lg:col-span-5 lg:pr-8">
                <p className="font-mono text-xs tracking-[0.06em] text-faint">{t('How I work').toLocaleUpperCase()}</p>
                <blockquote className="text-[26px] font-medium leading-[1.25] tracking-[-0.02em] text-balance lg:text-[30px]">
                    “{path.quote}”
                </blockquote>
                <p className="text-[16px] leading-[1.6] text-muted">{path.quote_note}</p>
            </div>

            <div className="flex flex-col lg:col-span-6 lg:col-start-7">
                <h2 className="pb-4 font-mono text-xs font-normal tracking-[0.06em] text-faint">{t('Where').toLocaleUpperCase()}</h2>
                <ol className="border-b border-line">
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
        </section>
    );
}

function TimelineRow({ when, title, children }) {
    return (
        <li className="grid gap-x-6 gap-y-1.5 border-t border-line py-6 sm:grid-cols-[150px_minmax(0,1fr)]">
            <span className="font-mono text-[13px] text-faint">{when}</span>
            <div className="flex flex-col gap-1.5">
                <span className="text-lg font-semibold tracking-[-0.01em]">{title}</span>
                <span className="text-[15px] leading-[1.5] text-muted">{children}</span>
            </div>
        </li>
    );
}

function Work({ projects }) {
    const { t } = useLocale();

    return (
        <section id="work" className="mx-auto flex max-w-[1440px] flex-col gap-8 px-5 pb-20 sm:px-10 lg:px-20 lg:pb-30">
            <h2 className={sectionTitle}>{t('Work')}</h2>
            <div className="grid gap-5 md:grid-cols-2">
                {projects.map((project, index) => (
                    <Reveal as="article" key={project.id} index={index} className="flex flex-col gap-3 rounded-[20px] border border-line bg-surface p-6 sm:p-8">
                        {project.year && <span className="font-mono text-xs text-faint">{project.year}</span>}
                        <h3 className="text-[22px] font-semibold tracking-[-0.015em]">{project.title}</h3>
                        {project.summary && <p className="text-[15px] leading-[1.55] text-muted">{project.summary}</p>}
                        {project.tech_stack.length > 0 && <TechChips stack={project.tech_stack} className="mt-1 flex flex-wrap gap-2" />}
                        {(project.repo_url || project.live_url) && (
                            <div className="mt-auto flex flex-wrap gap-x-5 pt-3 text-[15px]">
                                {project.repo_url && (
                                    <a
                                        href={project.repo_url}
                                        rel="noopener noreferrer"
                                        target="_blank"
                                        className="py-2 text-muted underline underline-offset-4 transition-colors hover:text-path"
                                    >
                                        {t('Repository')}
                                    </a>
                                )}
                                {project.live_url && (
                                    <a
                                        href={project.live_url}
                                        rel="noopener noreferrer"
                                        target="_blank"
                                        className="flex items-center gap-1.5 py-2 text-muted underline underline-offset-4 transition-colors hover:text-path"
                                    >
                                        {t('Live')}
                                        <Icon name="arrow-up-right" size={14} stroke={1.8} />
                                    </a>
                                )}
                            </div>
                        )}
                    </Reveal>
                ))}
            </div>
        </section>
    );
}

function NextPath({ next }) {
    const { locale, t } = useLocale();

    return (
        <Link to={`/${locale}/${next.key}`} className={`theme-${next.key} group block border-t border-line`}>
            <span className="mx-auto flex max-w-[1440px] items-center justify-between gap-6 px-5 py-14 sm:px-10 lg:px-20 lg:py-18">
                <span className="flex flex-col gap-3">
                    <span className="font-mono text-[13px] text-faint">
                        {t('Next path')} · {next.number}
                    </span>
                    <span className="text-[clamp(2rem,4.5vw,3.5rem)] font-semibold leading-[1.05] tracking-[-0.04em] text-balance">{next.title}</span>
                </span>
                <span className="flex size-16 shrink-0 items-center justify-center rounded-full bg-path text-on-path transition-transform group-hover:translate-x-1 lg:size-22">
                    <Icon name="arrow-right" size={28} stroke={1.8} />
                </span>
            </span>
        </Link>
    );
}
