/*
| A <Link> to a public page that starts loading its data on intent -- hover,
| keyboard focus or the start of a touch -- so the click finds it cached.
*/

import { Link } from 'react-router';
import { pageApi, prefetch } from '../lib/cache';

export function PageLink({ to, ...props }) {
    function warm() {
        const url = pageApi(to);

        if (url) {
            prefetch(url);
        }
    }

    return <Link to={to} onMouseEnter={warm} onFocus={warm} onTouchStart={warm} {...props} />;
}
