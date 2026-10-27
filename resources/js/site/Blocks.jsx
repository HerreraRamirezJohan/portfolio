/*
| Pieces shared by the home page and the path pages.
*/

import { ViewTransition } from 'react';
import { Reveal } from '../components/Reveal';
import { TechChips } from '../components/TechChip';
import { useLocale } from '../lib/i18n';

/**
 * One box of a request path. The focus box is named `path-{key}`, so it
 * travels between a home pipeline and that path page's flow.
 */
export function FlowBox({ node, pathKey, className = '' }) {
    const box = (
        <span
            className={`inline-block border px-3.5 py-2.5 text-[13px] uppercase tracking-[0.06em] ${
                node.focus ? 'border-signal bg-signal font-bold text-on-signal' : 'border-line-strong'
            } ${className}`}
        >
            {node.name}
        </span>
    );

    // At most one element per name may be mounted: only the focus node is named.
    return node.focus ? <ViewTransition name={`path-${pathKey}`}>{box}</ViewTransition> : box;
}

/**
 * Published projects as ruled cells. The first one leads; the rest stack
 * beside it.
 */
export function ProjectCells({ projects }) {
    const [lead, ...rest] = projects;

    return (
        <div className={`cells ${rest.length > 0 ? 'lg:grid-cols-[1.35fr_1fr]' : ''}`}>
            <Reveal as="article" className="flex flex-col justify-between gap-10 px-6 py-9 sm:px-9 lg:row-span-[var(--rows)]" style={{ '--rows': Math.max(rest.length, 1) }}>
                <div>
                    {lead.year && <p className="label">{lead.year}</p>}
                    <h3 className="macro mt-4 text-[clamp(1.75rem,3.2vw,2.75rem)] leading-[0.95]">{lead.title}</h3>
                    {(lead.description || lead.summary) && (
                        <p className="mt-6 max-w-[56ch] text-[15px] leading-[1.65] text-soft">{lead.description ?? lead.summary}</p>
                    )}
                </div>
                <ProjectFooter project={lead} />
            </Reveal>

            {rest.map((project, index) => (
                <Reveal as="article" key={project.id} index={index + 1} className="flex flex-col gap-3 px-6 py-8 sm:px-8">
                    {project.year && <p className="label">{project.year}</p>}
                    <h3 className="text-[19px] font-bold uppercase tracking-[0.02em]">{project.title}</h3>
                    {project.summary && <p className="text-[14.5px] leading-[1.65] text-soft">{project.summary}</p>}
                    <ProjectFooter project={project} />
                </Reveal>
            ))}
        </div>
    );
}

/**
 * A project's stack chips and its repository / live links.
 */
export function ProjectFooter({ project }) {
    const { t } = useLocale();
    const links = [
        [t('Repository'), project.repo_url],
        [t('Live'), project.live_url],
    ].filter(([, url]) => url);

    if (project.tech_stack.length === 0 && links.length === 0) {
        return null;
    }

    return (
        <div className="mt-2 flex flex-wrap items-center gap-x-5 gap-y-3">
            {project.tech_stack.length > 0 && <TechChips stack={project.tech_stack} />}
            {links.map(([label, url]) => (
                <a key={label} href={url} rel="noopener noreferrer" target="_blank" className="press py-2 text-[13px] uppercase tracking-[0.08em] text-signal hover:text-ink">
                    [ {label} ]
                </a>
            ))}
        </div>
    );
}
