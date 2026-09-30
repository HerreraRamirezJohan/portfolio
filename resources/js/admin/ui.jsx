/*
| Admin form building blocks (the old resources/views/components/admin/*).
*/

import { useSearchParams } from 'react-router';
import { config, isLocale } from '../lib/config';
import { useLocale } from '../lib/i18n';

export const inputClass = 'w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm dark:border-stone-700 dark:bg-stone-950';
export const primaryButton =
    'rounded-md bg-stone-900 px-4 py-2 text-sm font-medium text-stone-50 hover:bg-stone-800 disabled:opacity-60 dark:bg-stone-100 dark:text-stone-900';
export const secondaryButton = 'rounded-md border border-stone-300 px-4 py-2 text-sm dark:border-stone-700';
export const linkButton = 'text-sm text-stone-600 underline underline-offset-2 hover:text-stone-900 dark:text-stone-400';

/**
 * Immutably set `value` at `path` (an array of keys) inside `object`.
 */
export function setIn(object, [key, ...rest], value) {
    const copy = Array.isArray(object) ? [...object] : { ...object };
    copy[key] = rest.length ? setIn(object?.[key] ?? {}, rest, value) : value;

    return copy;
}

/**
 * The ES|EN tab that translatable fields edit, bound to ?lang= so it
 * survives a reload.
 */
export function useFormLocale() {
    const [params, setParams] = useSearchParams();
    const current = params.get('lang');
    const formLocale = isLocale(current) ? current : config.defaultLocale;

    const setFormLocale = (locale) => setParams((previous) => {
        const next = new URLSearchParams(previous);
        next.set('lang', locale);

        return next;
    }, { replace: true });

    return [formLocale, setFormLocale];
}

export function LocaleTabs({ current, onChange }) {
    return (
        <div className="inline-flex rounded-md border border-stone-300 p-0.5 dark:border-stone-700">
            {config.locales.map((locale) => (
                <button
                    key={locale}
                    type="button"
                    onClick={() => onChange(locale)}
                    aria-pressed={locale === current}
                    className={`rounded px-2.5 py-1 text-xs font-medium transition ${
                        locale === current
                            ? 'bg-stone-900 text-stone-50 dark:bg-stone-100 dark:text-stone-900'
                            : 'text-stone-500 hover:text-stone-900 dark:text-stone-400 dark:hover:text-stone-100'
                    }`}
                >
                    {locale.toUpperCase()}
                </button>
            ))}
        </div>
    );
}

export function PageHeader({ title, formLocale, setFormLocale, onAdd }) {
    const { t } = useLocale();

    return (
        <div className="mb-6 flex items-center justify-between">
            <h1 className="text-xl font-semibold text-stone-900 dark:text-stone-50">{title}</h1>
            <div className="flex items-center gap-3">
                <LocaleTabs current={formLocale} onChange={setFormLocale} />
                {onAdd && (
                    <button type="button" onClick={onAdd} className="rounded-md bg-stone-900 px-3 py-1.5 text-sm font-medium text-stone-50 hover:bg-stone-800 dark:bg-stone-100 dark:text-stone-900">
                        {t('Add')}
                    </button>
                )}
            </div>
        </div>
    );
}

export function Status({ message }) {
    if (! message) {
        return null;
    }

    return (
        <p role="status" className="mb-6 rounded-md bg-emerald-50 px-3 py-2 text-sm text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
            {message}
        </p>
    );
}

export function FieldError({ errors, name }) {
    if (! errors?.[name]) {
        return null;
    }

    return <p className="mt-1 text-sm text-red-600 dark:text-red-400">{errors[name]}</p>;
}

export function Field({ label, htmlFor, error, children }) {
    return (
        <div>
            <label className="block text-sm font-medium text-stone-700 dark:text-stone-300" htmlFor={htmlFor}>
                {label}
            </label>
            <div className="mt-1">{children}</div>
            {error && <p className="mt-1 text-sm text-red-600 dark:text-red-400">{error}</p>}
        </div>
    );
}

/**
 * A text input (or textarea with `rows`) bound to one form key path.
 */
export function Input({ form, setField, path, rows, className = inputClass, ...props }) {
    const value = path.reduce((object, key) => object?.[key], form) ?? '';
    const onChange = (event) => setField(path, event.target.value);

    return rows
        ? <textarea className={className} rows={rows} value={value} onChange={onChange} {...props} />
        : <input className={className} type="text" value={value} onChange={onChange} {...props} />;
}

/**
 * "Label (ES)" for a translatable field.
 */
export function localized(label, locale) {
    return `${label} (${locale.toUpperCase()})`;
}

export function FormButtons({ onCancel, saving }) {
    const { t } = useLocale();

    return (
        <div className="flex gap-3">
            <button className={primaryButton} type="submit" disabled={saving}>
                {t('Save')}
            </button>
            {onCancel && (
                <button className={secondaryButton} type="button" onClick={onCancel}>
                    {t('Cancel')}
                </button>
            )}
        </div>
    );
}

export function RowActions({ onUp, onDown, onEdit, onDelete }) {
    const { t } = useLocale();

    return (
        <div className="flex shrink-0 items-center gap-3 text-sm">
            <button className="text-stone-500 hover:text-stone-900 dark:hover:text-stone-100" type="button" title={t('Move up')} aria-label={t('Move up')} onClick={onUp}>
                ↑
            </button>
            <button className="text-stone-500 hover:text-stone-900 dark:hover:text-stone-100" type="button" title={t('Move down')} aria-label={t('Move down')} onClick={onDown}>
                ↓
            </button>
            <button className="text-stone-600 underline underline-offset-2 hover:text-stone-900 dark:text-stone-400" type="button" onClick={onEdit}>
                {t('Edit')}
            </button>
            <button className="text-red-600 hover:underline dark:text-red-400" type="button" onClick={onDelete}>
                {t('Delete')}
            </button>
        </div>
    );
}
