/*
| Server-provided settings, read once from the shell's #app[data-config]
| (see SpaController). Keeps locales and path keys in one place: PHP config.
*/

const root = document.getElementById('app');

/** @type {{ locales: string[], defaultLocale: string, paths: string[] }} */
export const config = JSON.parse(root?.dataset.config ?? '{}');

export const isLocale = (value) => config.locales.includes(value);
