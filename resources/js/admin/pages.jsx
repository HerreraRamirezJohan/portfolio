/*
| The admin screens that are not ordered collections: login, dashboard
| and the profile form.
*/

import { useRef, useState } from 'react';
import { Link, useNavigate } from 'react-router';
import { csrfCookie, http, validationErrors } from '../lib/http';
import { useLocale } from '../lib/i18n';
import { useApi } from '../lib/useApi';
import { Field, FieldError, FormButtons, Input, inputClass, localized, LocaleTabs, primaryButton, secondaryButton, setIn, Status, useFormLocale } from './ui';

/*
|--------------------------------------------------------------------------
| Login -- Sanctum SPA authentication
|--------------------------------------------------------------------------
*/

export function Login() {
    const { t } = useLocale();
    const navigate = useNavigate();
    const [form, setForm] = useState({ email: '', password: '', remember: false });
    const [errors, setErrors] = useState({});
    const [busy, setBusy] = useState(false);

    async function submit(event) {
        event.preventDefault();
        setBusy(true);

        try {
            await csrfCookie();
            await http.post('/login', form);
            navigate('/admin', { replace: true });
        } catch (error) {
            setErrors(validationErrors(error));
            setBusy(false);
        }
    }

    const set = (key) => (event) => setForm({ ...form, [key]: event.target.type === 'checkbox' ? event.target.checked : event.target.value });

    return (
        <div className="mx-auto max-w-sm">
            <h1 className="text-xl font-semibold text-stone-900 dark:text-stone-50">{t('Log in')}</h1>

            <form className="mt-6 space-y-4" onSubmit={submit}>
                <Field label={t('Email')} htmlFor="email" error={errors.email}>
                    <input className={inputClass} id="email" type="email" autoComplete="username" required value={form.email} onChange={set('email')} />
                </Field>

                <Field label={t('Password')} htmlFor="password" error={errors.password}>
                    <input className={inputClass} id="password" type="password" autoComplete="current-password" required value={form.password} onChange={set('password')} />
                </Field>

                <label className="flex items-center gap-2 text-sm">
                    <input className="rounded border-stone-300 dark:border-stone-700" type="checkbox" checked={form.remember} onChange={set('remember')} />
                    {t('Remember me')}
                </label>

                <button className={`${primaryButton} w-full`} type="submit" disabled={busy}>
                    {t('Log in')}
                </button>
            </form>
        </div>
    );
}

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

export function Dashboard() {
    const { t } = useLocale();
    const { data } = useApi('/admin/dashboard');

    if (! data) {
        return null;
    }

    const cards = [
        ['/admin/experience', t('Experience'), data.counts.experiences],
        ['/admin/education', t('Education'), data.counts.educations],
        ['/admin/skills', t('Technical Skills'), data.counts.skill_groups],
        ['/admin/languages', t('Languages'), data.counts.languages],
        ['/admin/projects', t('Projects'), data.counts.projects],
        ['/admin/services', t('Services'), data.counts.services],
        ['/admin/process', t('Process'), data.counts.process_steps],
        ['/admin/faq', t('FAQ'), data.counts.faqs],
        ['/admin/costs', t('Cost factors'), data.counts.cost_factors],
    ];

    return (
        <div>
            <h1 className="text-xl font-semibold text-stone-900 dark:text-stone-50">{t('Dashboard')}</h1>
            <p className="mt-1 text-sm text-stone-600 dark:text-stone-400">
                {data.name} — {data.email}
            </p>

            <div className="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                {cards.map(([to, label, count]) => (
                    <Link
                        key={to}
                        to={to}
                        className="rounded-lg border border-stone-200 bg-white p-4 transition hover:border-stone-400 dark:border-stone-800 dark:bg-stone-900 dark:hover:border-stone-600"
                    >
                        <p className="text-sm text-stone-500 dark:text-stone-400">{label}</p>
                        <p className="mt-1 text-2xl font-semibold text-stone-900 dark:text-stone-50">{count}</p>
                    </Link>
                ))}
            </div>
        </div>
    );
}

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

export function ProfileForm() {
    const { data } = useApi('/admin/profile');

    // Mount the form only once the data exists, so its state starts filled.
    return data ? <ProfileEditor initial={data} /> : null;
}

function ProfileEditor({ initial }) {
    const { t } = useLocale();
    const [formLocale, setFormLocale] = useFormLocale();
    const [form, setForm] = useState(initial);
    const [errors, setErrors] = useState({});
    const [status, setStatus] = useState(null);
    const [saving, setSaving] = useState(false);

    const setField = (path, value) => setForm((previous) => setIn(previous, path, value));

    async function save(event) {
        event.preventDefault();
        setSaving(true);
        setStatus(null);

        try {
            const response = await http.put('/admin/profile', form);
            setForm(response.data.data);
            setErrors({});
            setStatus(t('Saved.'));
        } catch (error) {
            setErrors(validationErrors(error));
        } finally {
            setSaving(false);
        }
    }

    const contacts = [
        ['phone', t('Phone')],
        ['public_email', t('Email')],
        ['linkedin_url', 'LinkedIn URL'],
        ['github_url', 'GitHub URL'],
        ['website_url', t('Website')],
    ];

    return (
        <div>
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-xl font-semibold text-stone-900 dark:text-stone-50">{t('Profile')}</h1>
                <LocaleTabs current={formLocale} onChange={setFormLocale} />
            </div>

            <Status message={status} />

            <PhotoField initialUrl={initial.photo_url} />

            <form className="space-y-5" onSubmit={save}>
                <Field label={t('Name')} htmlFor="name" error={errors.name}>
                    <Input form={form} setField={setField} path={['name']} id="name" />
                </Field>

                <Field label={localized(t('Headline'), formLocale)}>
                    <Input form={form} setField={setField} path={['headline', formLocale]} />
                    <FieldError errors={errors} name={`headline.${formLocale}`} />
                </Field>

                <Field label={localized(t('Location'), formLocale)}>
                    <Input form={form} setField={setField} path={['location', formLocale]} />
                </Field>

                <Field label={localized(t('Profile'), formLocale)}>
                    <Input form={form} setField={setField} path={['summary', formLocale]} rows={7} />
                </Field>

                <div className="grid gap-5 sm:grid-cols-2">
                    {contacts.map(([key, label]) => (
                        <Field key={key} label={label} error={errors[key]}>
                            <Input form={form} setField={setField} path={[key]} />
                        </Field>
                    ))}
                </div>

                <FormButtons saving={saving} />
            </form>
        </div>
    );
}

/**
 * The profile photo. It saves on its own (a file can't ride along in the JSON
 * form), so picking or removing a photo takes effect immediately.
 */
function PhotoField({ initialUrl }) {
    const { t } = useLocale();
    const input = useRef(null);
    const [url, setUrl] = useState(initialUrl);
    const [error, setError] = useState(null);
    const [busy, setBusy] = useState(false);

    async function upload(event) {
        const file = event.target.files[0];

        if (! file) {
            return;
        }

        const body = new FormData();
        body.append('photo', file);

        setBusy(true);
        setError(null);

        try {
            const response = await http.post('/admin/profile/photo', body);
            setUrl(response.data.data.photo_url);
        } catch (failure) {
            setError(validationErrors(failure).photo ?? t('Something went wrong.'));
        } finally {
            setBusy(false);
            // Allow picking the same file again after a failure.
            event.target.value = '';
        }
    }

    async function remove() {
        setBusy(true);
        setError(null);

        try {
            await http.delete('/admin/profile/photo');
            setUrl(null);
        } catch {
            setError(t('Something went wrong.'));
        } finally {
            setBusy(false);
        }
    }

    return (
        <div className="mb-6 flex items-center gap-4">
            {url ? (
                <img src={url} alt="" className="size-20 rounded-md object-cover" />
            ) : (
                <div className="size-20 rounded-md border border-dashed border-stone-300 dark:border-stone-700" aria-hidden="true" />
            )}

            <div>
                <p className="text-sm font-medium text-stone-700 dark:text-stone-300">{t('Photo')}</p>
                <div className="mt-1 flex gap-3">
                    <button type="button" className={secondaryButton} disabled={busy} onClick={() => input.current.click()}>
                        {busy ? t('Uploading…') : t('Upload photo')}
                    </button>
                    {url && (
                        <button type="button" className={secondaryButton} disabled={busy} onClick={remove}>
                            {t('Remove photo')}
                        </button>
                    )}
                </div>
                <input ref={input} type="file" accept="image/jpeg,image/png,image/webp" className="hidden" onChange={upload} />
                {error && <p role="alert" className="mt-1 text-sm text-red-600 dark:text-red-400">{error}</p>}
            </div>
        </div>
    );
}
