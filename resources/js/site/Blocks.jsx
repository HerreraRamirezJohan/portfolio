/*
| Pieces shared by the home page and the path pages.
*/

import { ViewTransition } from 'react';
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
