/*
| UI strings, translated from the same lang/*.json files Laravel uses.
|
| Keys are the English text, exactly as with Laravel's __(): a missing
| translation falls back to the key. Content from the database and the
| career-path copy in config/portfolio.php arrive already translated from
| the API; this only covers the labels that live in the components.
*/

import { createContext, Fragment, useContext } from 'react';
import en from '../../../lang/en.json';
import es from '../../../lang/es.json';
import { config } from './config';

const catalogs = { en, es };

function line(locale, key) {
    return catalogs[locale]?.[key] ?? key;
}

function replace(text, replacements) {
    return Object.entries(replacements).reduce(
        (result, [name, value]) => result.replaceAll(`:${name}`, String(value)),
        text,
    );
}

/**
 * __('key', {name: value}) in the given locale.
 */
export function translate(locale, key, replacements = {}) {
    return replace(line(locale, key), replacements);
}

/**
 * trans_choice() for the simple "one|many" form used in lang/*.json.
 */
export function translateChoice(locale, key, count, replacements = {}) {
    const [one, many = one] = line(locale, key).split('|');

    return replace(count === 1 ? one : many, { count, ...replacements });
}

const LocaleContext = createContext(config.defaultLocale);

export function LocaleProvider({ locale, children }) {
    return <LocaleContext.Provider value={locale}>{children}</LocaleContext.Provider>;
}

export function useLocale() {
    const locale = useContext(LocaleContext);

    return {
        locale,
        t: (key, replacements) => translate(locale, key, replacements),
        choice: (key, count, replacements) => translateChoice(locale, key, count, replacements),
    };
}

/**
 * Translate a line whose placeholders are React nodes rather than strings,
 * e.g. <Trans k="I build across :sap..." values={{ sap: <span>SAP</span> }} />.
 */
export function Trans({ k, values }) {
    const { locale } = useLocale();
    const names = Object.keys(values).sort((a, b) => b.length - a.length);
    const pattern = new RegExp(`:(${names.join('|')})`, 'g');

    return line(locale, k)
        .split(pattern)
        .map((part, i) => <Fragment key={i}>{i % 2 === 1 ? values[part] : part}</Fragment>);
}
