import { useCallback, useEffect, useState } from 'react';
import { http } from './http';

/**
 * GET an API endpoint and keep its `data` in state. Re-fetches when the
 * URL changes and ignores responses for a URL that is no longer current.
 */
export function useApi(url) {
    const [state, setState] = useState({ data: null, error: null, loading: true });
    const [version, setVersion] = useState(0);

    useEffect(() => {
        const controller = new AbortController();

        setState((previous) => ({ ...previous, loading: true, error: null }));

        http.get(url, { signal: controller.signal })
            .then((response) => setState({ data: response.data.data, error: null, loading: false }))
            .catch((error) => {
                if (! controller.signal.aborted) {
                    setState({ data: null, error, loading: false });
                }
            });

        return () => controller.abort();
    }, [url, version]);

    const reload = useCallback(() => setVersion((v) => v + 1), []);

    return { ...state, reload, setData: (data) => setState((s) => ({ ...s, data })) };
}
