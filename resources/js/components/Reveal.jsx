/*
| Fades an element in the first time it enters the viewport. `index`
| staggers siblings within a group. With reduced motion, or without
| IntersectionObserver, the element is simply shown.
*/

import { useEffect, useRef, useState } from 'react';

const reducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

export function Reveal({ as: Tag = 'div', index = 0, className = '', style, children, ...props }) {
    const ref = useRef(null);
    const [visible, setVisible] = useState(() => reducedMotion() || ! ('IntersectionObserver' in window));

    useEffect(() => {
        if (visible || ! ref.current) {
            return;
        }

        const observer = new IntersectionObserver(
            ([entry]) => {
                // One-shot: re-animating on scroll-back is noise.
                if (entry.isIntersecting) {
                    setVisible(true);
                    observer.disconnect();
                }
            },
            { rootMargin: '0px 0px -12% 0px', threshold: 0.1 },
        );

        observer.observe(ref.current);

        return () => observer.disconnect();
    }, [visible]);

    return (
        <Tag
            ref={ref}
            className={`reveal ${visible ? 'is-visible' : ''} ${className}`}
            style={{ '--reveal-delay': `${index * 70}ms`, ...style }}
            {...props}
        >
            {children}
        </Tag>
    );
}
