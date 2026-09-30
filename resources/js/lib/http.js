/*
| The one HTTP client for routes/api.php.
|
| Configured for Sanctum SPA authentication: the session cookie is sent with
| every request and axios copies the XSRF-TOKEN cookie into the
| X-XSRF-TOKEN header, so no API token ever touches JavaScript.
*/

import axios from 'axios';

export const http = axios.create({
    baseURL: '/api',
    withCredentials: true,
    withXSRFToken: true,
    headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
});

/**
 * Initialise CSRF protection before logging in (sets the XSRF-TOKEN cookie).
 */
export function csrfCookie() {
    return http.get('/sanctum/csrf-cookie', { baseURL: '/' });
}

/**
 * A signed-out or expired admin session answers 401 (unauthenticated) or
 * 419 (CSRF token mismatch). Either way the only way forward is to log in.
 */
http.interceptors.response.use(undefined, (error) => {
    const status = error.response?.status;
    const inAdmin = window.location.pathname.startsWith('/admin');
    const onLogin = window.location.pathname === '/admin/login';

    if ((status === 401 || status === 419) && inAdmin && ! onLogin) {
        window.location.assign('/admin/login');
    }

    return Promise.reject(error);
});

/**
 * Validation errors from a 422 as {"field.path": "first message"}.
 */
export function validationErrors(error) {
    const errors = error.response?.status === 422 ? error.response.data.errors ?? {} : {};

    return Object.fromEntries(Object.entries(errors).map(([key, messages]) => [key, messages[0]]));
}
