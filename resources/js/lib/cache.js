/*
| In-memory cache for the public pages' API payloads.
|
| One promise per URL: concurrent callers share a request, a hover can warm
| a page before the click, and a page already seen renders at once. Entries
| carry the `status`/`value` fields React's use() reads, so a settled one
| never suspends. The shell inlines the first payload (see SpaController),
| which seeds the cache before React starts.
*/

import { config } from './config';
import { http } from './http';

const TTL = 5 * 60 * 1000;

/** @type {Map<string, { promise: Promise<any>, at: number }>} */
const entries = new Map();

function settled(data) {
    const promise = Promise.resolve(data);
    promise.status = 'fulfilled';
    promise.value = data;

    return promise;
}

function request(url) {
    const promise = http.get(url).then((response) => response.data.data);

    promise.status = 'pending';
    promise.then(
        (data) => {
            promise.status = 'fulfilled';
            promise.value = data;
        },
        (error) => {
            promise.status = 'rejected';
            promise.reason = error;

            // A failure is not cached: the next visit tries again.
            if (entries.get(url)?.promise === promise) {
                entries.delete(url);
            }
        },
    );

    return promise;
}

/**
 * The payload for an API URL, as a promise that stays the same while fresh.
 */
export function resource(url) {
    const entry = entries.get(url);

    if (entry && Date.now() - entry.at < TTL) {
        return entry.promise;
    }

    const promise = request(url);
    entries.set(url, { promise, at: Date.now() });

    return promise;
}

/**
 * Warm the cache without caring about the result.
 */
export function prefetch(url) {
    resource(url).catch(() => {});
}

/**
 * The API URL behind a public page: /es -> /es/portfolio, /es/sap -> /es/paths/sap.
 */
export function pageApi(pathname) {
    const [locale, path, ...rest] = pathname.split(/[?#]/)[0].split('/').filter(Boolean);

    if (! config.locales.includes(locale) || rest.length > 0) {
        return null;
    }

    if (! path) {
        return `/${locale}/portfolio`;
    }

    return config.paths.includes(path) ? `/${locale}/paths/${path}` : null;
}

const bootstrap = document.getElementById('bootstrap');

if (bootstrap) {
    try {
        entries.set(bootstrap.dataset.url, { promise: settled(JSON.parse(bootstrap.textContent).data), at: Date.now() });
    } catch {
        // A malformed inline payload just means the page fetches as usual.
    }
}
