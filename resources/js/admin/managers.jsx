/*
| The admin collections. Each is configuration for CollectionManager:
| its endpoint under /api/admin, a blank record, the form and a list row.
*/

import { useLocale } from '../lib/i18n';
import { blankTranslations, CollectionManager } from './CollectionManager';
import { Field, FieldError, Input, inputClass, linkButton, localized } from './ui';

function Checkbox({ label, checked, onChange }) {
    return (
        <label className="flex items-center gap-2 text-sm">
            <input className="rounded border-stone-300 dark:border-stone-700" type="checkbox" checked={checked} onChange={(event) => onChange(event.target.checked)} />
            {label}
        </label>
    );
}

function Title({ children }) {
    return <p className="font-medium text-stone-900 dark:text-stone-50">{children}</p>;
}

function Subtitle({ children }) {
    return <p className="text-sm text-stone-600 dark:text-stone-400">{children || '—'}</p>;
}

function Meta({ children }) {
    return <p className="mt-0.5 text-xs text-stone-500">{children}</p>;
}

/*
|--------------------------------------------------------------------------
| Experience
|--------------------------------------------------------------------------
*/

export function ExperienceManager() {
    const { t } = useLocale();

    return (
        <CollectionManager
            title={t('Experience')}
            endpoint="/admin/experiences"
            blank={() => ({
                company: '',
                start_date: '',
                end_date: '',
                is_current: false,
                role: blankTranslations(),
                location: blankTranslations(),
                bullets: blankTranslations([]),
            })}
            renderRow={(row, locale) => (
                <>
                    <Title>{row.company}</Title>
                    <Subtitle>{row.role[locale]}</Subtitle>
                    <Meta>{row.date_range}</Meta>
                </>
            )}
            renderForm={({ form, setField, errors, locale }) => {
                const bullets = form.bullets[locale] ?? [];

                return (
                    <>
                        <div className="grid gap-5 sm:grid-cols-2">
                            <Field label={t('Company')} error={errors.company}>
                                <Input form={form} setField={setField} path={['company']} />
                            </Field>
                            <Field label={localized(t('Location'), locale)}>
                                <Input form={form} setField={setField} path={['location', locale]} />
                            </Field>
                            <Field label={localized(t('Role'), locale)}>
                                <Input form={form} setField={setField} path={['role', locale]} />
                            </Field>
                            <div className="grid grid-cols-2 gap-3">
                                <Field label={t('Start')} error={errors.start_date}>
                                    <Input form={form} setField={setField} path={['start_date']} type="date" />
                                </Field>
                                <Field label={t('End')} error={errors.end_date}>
                                    <Input
                                        form={form}
                                        setField={setField}
                                        path={['end_date']}
                                        type="date"
                                        disabled={form.is_current}
                                        className={`${inputClass} disabled:opacity-40`}
                                    />
                                </Field>
                            </div>
                        </div>

                        <Checkbox label={t('Current role')} checked={form.is_current} onChange={(value) => setField(['is_current'], value)} />

                        <div>
                            <div className="flex items-center justify-between">
                                <span className="text-sm font-medium text-stone-700 dark:text-stone-300">{localized(t('Bullets'), locale)}</span>
                                <button className={linkButton} type="button" onClick={() => setField(['bullets', locale], [...bullets, { label: '', body: '' }])}>
                                    {t('Add')}
                                </button>
                            </div>

                            <div className="mt-2 space-y-3">
                                {bullets.map((bullet, index) => (
                                    <div key={`${locale}-${index}`} className="rounded-md border border-stone-200 p-3 dark:border-stone-800">
                                        <Input
                                            form={form}
                                            setField={setField}
                                            path={['bullets', locale, index, 'label']}
                                            placeholder={t('Label (optional)')}
                                            aria-label={t('Label (optional)')}
                                        />
                                        <Input
                                            form={form}
                                            setField={setField}
                                            path={['bullets', locale, index, 'body']}
                                            rows={3}
                                            placeholder={t('Body')}
                                            aria-label={t('Body')}
                                            className={`${inputClass} mt-2`}
                                        />
                                        <FieldError errors={errors} name={`bullets.${locale}.${index}.body`} />
                                        <button
                                            className="mt-1 text-xs text-red-600 hover:underline dark:text-red-400"
                                            type="button"
                                            onClick={() => setField(['bullets', locale], bullets.filter((_, i) => i !== index))}
                                        >
                                            {t('Delete')}
                                        </button>
                                    </div>
                                ))}
                                {bullets.length === 0 && <p className="text-sm text-stone-500 dark:text-stone-400">{t('No items yet.')}</p>}
                            </div>
                        </div>
                    </>
                );
            }}
        />
    );
}

/*
|--------------------------------------------------------------------------
| Education
|--------------------------------------------------------------------------
*/

export function EducationManager() {
    const { t } = useLocale();

    return (
        <CollectionManager
            title={t('Education')}
            endpoint="/admin/educations"
            blank={() => ({
                institution: '',
                start_year: '',
                end_year: '',
                degree: blankTranslations(),
                location: blankTranslations(),
                notes: blankTranslations(),
            })}
            toForm={(row) => ({ ...row, start_year: row.start_year ?? '', end_year: row.end_year ?? '' })}
            renderRow={(row, locale) => (
                <>
                    <Title>{row.institution}</Title>
                    <Subtitle>{row.degree[locale]}</Subtitle>
                    <Meta>{[row.start_year, row.end_year].filter(Boolean).join(' – ')}</Meta>
                </>
            )}
            renderForm={({ form, setField, errors, locale }) => (
                <>
                    <div className="grid gap-5 sm:grid-cols-2">
                        <Field label={t('Institution')} error={errors.institution}>
                            <Input form={form} setField={setField} path={['institution']} />
                        </Field>
                        <Field label={localized(t('Location'), locale)}>
                            <Input form={form} setField={setField} path={['location', locale]} />
                        </Field>
                        <Field label={localized(t('Degree'), locale)}>
                            <Input form={form} setField={setField} path={['degree', locale]} />
                        </Field>
                        <div className="grid grid-cols-2 gap-3">
                            <Field label={t('Start')} error={errors.start_year}>
                                <Input form={form} setField={setField} path={['start_year']} type="number" />
                            </Field>
                            <Field label={t('End')} error={errors.end_year}>
                                <Input form={form} setField={setField} path={['end_year']} type="number" />
                            </Field>
                        </div>
                    </div>
                    <Field label={localized(t('Notes'), locale)}>
                        <Input form={form} setField={setField} path={['notes', locale]} rows={3} />
                    </Field>
                </>
            )}
        />
    );
}

/*
|--------------------------------------------------------------------------
| Technical skills
|--------------------------------------------------------------------------
*/

export function SkillGroupManager() {
    const { t } = useLocale();

    return (
        <CollectionManager
            title={t('Technical Skills')}
            endpoint="/admin/skill-groups"
            blank={() => ({ label: blankTranslations(), description: blankTranslations(), skills: [] })}
            renderRow={(row, locale) => (
                <>
                    <Title>{row.label[locale] || '—'}</Title>
                    <p className="mt-1 text-sm text-stone-600 dark:text-stone-400">{row.skills.join(', ') || '—'}</p>
                </>
            )}
            renderForm={({ form, setField, locale }) => (
                <>
                    <Field label={localized(t('Label'), locale)}>
                        <Input form={form} setField={setField} path={['label', locale]} />
                    </Field>
                    <Field label={localized(t('Description'), locale)}>
                        <Input form={form} setField={setField} path={['description', locale]} rows={3} />
                    </Field>

                    <div>
                        <div className="flex items-center justify-between">
                            <span className="text-sm font-medium text-stone-700 dark:text-stone-300">{t('Skills')}</span>
                            <button className={linkButton} type="button" onClick={() => setField(['skills'], [...form.skills, ''])}>
                                {t('Add')}
                            </button>
                        </div>
                        <p className="mt-1 text-xs text-stone-500">{t('Not translated — proper nouns like PHP, React, Docker.')}</p>

                        <div className="mt-2 grid gap-2 sm:grid-cols-2">
                            {form.skills.map((_, index) => (
                                <div key={index} className="flex items-center gap-2">
                                    <Input form={form} setField={setField} path={['skills', index]} aria-label={t('Skills')} />
                                    <button
                                        className="shrink-0 text-xs text-red-600 hover:underline dark:text-red-400"
                                        type="button"
                                        aria-label={t('Delete')}
                                        onClick={() => setField(['skills'], form.skills.filter((__, i) => i !== index))}
                                    >
                                        ✕
                                    </button>
                                </div>
                            ))}
                            {form.skills.length === 0 && <p className="text-sm text-stone-500 dark:text-stone-400">{t('No items yet.')}</p>}
                        </div>
                    </div>
                </>
            )}
        />
    );
}

/*
|--------------------------------------------------------------------------
| Languages
|--------------------------------------------------------------------------
*/

export function LanguageManager() {
    const { t } = useLocale();

    return (
        <CollectionManager
            title={t('Languages')}
            endpoint="/admin/languages"
            blank={() => ({ code: '', name: blankTranslations(), level_label: blankTranslations() })}
            renderRow={(row, locale) => (
                <>
                    <Title>{row.name[locale] || row.code}</Title>
                    <Subtitle>{row.level_label[locale]}</Subtitle>
                </>
            )}
            renderForm={({ form, setField, errors, locale }) => (
                <div className="grid gap-5 sm:grid-cols-3">
                    <Field label={t('Code')} error={errors.code}>
                        <Input form={form} setField={setField} path={['code']} placeholder="es" />
                    </Field>
                    <Field label={localized(t('Name'), locale)}>
                        <Input form={form} setField={setField} path={['name', locale]} />
                    </Field>
                    <Field label={localized(t('Level'), locale)}>
                        <Input form={form} setField={setField} path={['level_label', locale]} />
                    </Field>
                </div>
            )}
        />
    );
}

/*
|--------------------------------------------------------------------------
| Projects
|--------------------------------------------------------------------------
*/

function slugify(text) {
    return text
        .normalize('NFD')
        .replace(/[̀-ͯ]/g, '')
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

export function ProjectManager() {
    const { t } = useLocale();

    return (
        <CollectionManager
            title={t('Projects')}
            endpoint="/admin/projects"
            blank={() => ({
                slug: '',
                repo_url: '',
                live_url: '',
                is_published: true,
                year: '',
                tech_stack_csv: '',
                title: blankTranslations(),
                summary: blankTranslations(),
                description: blankTranslations(),
            })}
            // Edited as a comma-separated string; sent as an array.
            toForm={(row) => ({ ...row, tech_stack_csv: row.tech_stack.join(', ') })}
            toPayload={({ tech_stack_csv, ...form }) => ({
                ...form,
                tech_stack: tech_stack_csv.split(',').map((item) => item.trim()).filter(Boolean),
            })}
            renderRow={(row, locale) => (
                <>
                    <Title>
                        {row.title[locale] || row.slug}
                        {! row.is_published && (
                            <span className="ml-1 rounded bg-stone-200 px-1.5 py-0.5 text-xs text-stone-600 dark:bg-stone-800 dark:text-stone-400">{t('Draft')}</span>
                        )}
                    </Title>
                    <Subtitle>{row.summary[locale]}</Subtitle>
                </>
            )}
            renderForm={({ form, setField, errors, locale, isNew }) => (
                <>
                    <Field label={localized(t('Title'), locale)}>
                        <Input
                            form={form}
                            setField={setField}
                            path={['title', locale]}
                            // Suggest a slug from the title the first time one is typed.
                            onBlur={(event) => isNew && ! form.slug && setField(['slug'], slugify(event.target.value))}
                        />
                    </Field>

                    <div className="grid gap-5 sm:grid-cols-3">
                        <Field label={t('Slug')} error={errors.slug}>
                            <Input form={form} setField={setField} path={['slug']} />
                        </Field>
                        <Field label="Repository URL" error={errors.repo_url}>
                            <Input form={form} setField={setField} path={['repo_url']} />
                        </Field>
                        <Field label="Live URL" error={errors.live_url}>
                            <Input form={form} setField={setField} path={['live_url']} />
                        </Field>
                    </div>

                    <div className="grid gap-5 sm:grid-cols-3">
                        <Field label={t('Year')} error={errors.year}>
                            <Input form={form} setField={setField} path={['year']} placeholder="2024 – 2026" />
                        </Field>
                        <div className="sm:col-span-2">
                            <Field label={t('Tech stack')} error={errors.tech_stack}>
                                <Input form={form} setField={setField} path={['tech_stack_csv']} placeholder="Laravel, PostgreSQL, Docker" />
                                <p className="mt-1 text-xs text-stone-500 dark:text-stone-400">
                                    {t('Comma separated. Names matching the icon set render with their mark.')}
                                </p>
                            </Field>
                        </div>
                    </div>

                    <Field label={localized(t('Summary'), locale)}>
                        <Input form={form} setField={setField} path={['summary', locale]} rows={2} />
                    </Field>
                    <Field label={localized(t('Description'), locale)}>
                        <Input form={form} setField={setField} path={['description', locale]} rows={5} />
                    </Field>

                    <Checkbox label={t('Published')} checked={form.is_published} onChange={(value) => setField(['is_published'], value)} />
                </>
            )}
        />
    );
}
