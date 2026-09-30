/*
| The one CRUD screen every ordered admin collection shares -- the React
| counterpart of the old *Manager Livewire components.
|
| A manager supplies its endpoint, a blank form, how to render the form and
| a list row, and optionally how to convert between a row and the form
| (toForm / toPayload). Everything else -- list, create, edit, save,
| delete, move up/down, validation errors, the ES|EN tab -- lives here.
*/

import { useState } from 'react';
import { config } from '../lib/config';
import { http, validationErrors } from '../lib/http';
import { useLocale } from '../lib/i18n';
import { useApi } from '../lib/useApi';
import { FormButtons, PageHeader, RowActions, setIn, Status, useFormLocale } from './ui';

const NEW = 'new';

export function CollectionManager({ title, endpoint, blank, toForm = (row) => row, toPayload = (form) => form, renderForm, renderRow }) {
    const { t } = useLocale();
    const [formLocale, setFormLocale] = useFormLocale();
    const { data: rows, setData: setRows, reload } = useApi(endpoint);

    const [editing, setEditing] = useState(null);
    const [form, setForm] = useState(blank);
    const [errors, setErrors] = useState({});
    const [status, setStatus] = useState(null);
    const [saving, setSaving] = useState(false);

    const setField = (path, value) => setForm((previous) => setIn(previous, path, value));

    function reset() {
        setEditing(null);
        setForm(blank());
        setErrors({});
    }

    function create() {
        reset();
        setStatus(null);
        setEditing(NEW);
    }

    function edit(row) {
        setErrors({});
        setStatus(null);
        setForm(toForm(row));
        setEditing(row.id);
    }

    async function save(event) {
        event.preventDefault();
        setSaving(true);

        try {
            const payload = toPayload(form);

            if (editing === NEW) {
                await http.post(endpoint, payload);
            } else {
                await http.put(`${endpoint}/${editing}`, payload);
            }

            reset();
            reload();
            setStatus(t('Saved.'));
        } catch (error) {
            setErrors(validationErrors(error));
        } finally {
            setSaving(false);
        }
    }

    async function remove(row) {
        if (! window.confirm(t('Are you sure?'))) {
            return;
        }

        await http.delete(`${endpoint}/${row.id}`);

        if (editing === row.id) {
            reset();
        }

        reload();
        setStatus(t('Deleted.'));
    }

    async function move(row, direction) {
        const response = await http.patch(`${endpoint}/${row.id}/move`, { direction });
        setRows(response.data.data);
    }

    return (
        <div>
            <PageHeader title={title} formLocale={formLocale} setFormLocale={setFormLocale} onAdd={create} />
            <Status message={status} />

            {editing !== null && (
                <form
                    onSubmit={save}
                    className="mb-8 space-y-5 rounded-lg border border-stone-300 bg-white p-5 dark:border-stone-700 dark:bg-stone-900"
                >
                    {renderForm({ form, setField, errors, locale: formLocale, isNew: editing === NEW })}
                    <FormButtons onCancel={reset} saving={saving} />
                </form>
            )}

            <div className="space-y-3">
                {rows?.map((row) => (
                    <div
                        key={row.id}
                        className="flex items-start justify-between gap-4 rounded-lg border border-stone-200 bg-white p-4 dark:border-stone-800 dark:bg-stone-900"
                    >
                        <div>{renderRow(row, formLocale)}</div>
                        <RowActions
                            onUp={() => move(row, 'up')}
                            onDown={() => move(row, 'down')}
                            onEdit={() => edit(row)}
                            onDelete={() => remove(row)}
                        />
                    </div>
                ))}

                {rows?.length === 0 && <p className="text-sm text-stone-500 dark:text-stone-400">{t('No items yet.')}</p>}
            </div>
        </div>
    );
}

/**
 * {es: '', en: ''} for a new translatable field.
 */
export function blankTranslations(value = '') {
    return Object.fromEntries(config.locales.map((locale) => [locale, structuredClone(value)]));
}
