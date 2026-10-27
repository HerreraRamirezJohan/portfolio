/*
| /{locale} -- the freelance home page. A plain, light page for business
| owners: what I do and its hourly rate, what adds to the cost, how it
| works, questions, and one way to reach me (email). The CV it points to
| lives at /{locale}/cv and keeps the dark Ledger look; this page is
| `.site-light`. Data: GET /api/{locale}/home.
*/

import { useEffect } from 'react';
import { Reveal } from '../components/Reveal';
import { prefetch } from '../lib/cache';
import { formatMoney } from '../lib/format';
import { useLocale } from '../lib/i18n';
import { mailto, socials } from '../site/Contact';
import { HomeFooter } from '../site/HomeFooter';
import { HomeHeader } from '../site/HomeHeader';
import { PageLink } from '../site/PageLink';
import { useDocumentMeta, usePage } from '../site/PublicLayout';

const wrap = 'mx-auto max-w-6xl px-4 sm:px-6 lg:px-8';
const heading = 'font-display text-[clamp(1.9rem,4.2vw,3rem)] font-extrabold leading-[1.05] tracking-[-0.03em] text-balance';

export function Home() {
    const { locale, t } = useLocale();
    const data = usePage(`/${locale}/home`);
    const { person, services, cost_factors: factors, process: steps, faqs, experience } = data;

    useDocumentMeta(data.meta);
    useBrowserColor('#fbfaf7');

    // The CV and the path pages the services point to are the places to go
    // from here: load them once the browser is idle so a tap with no hover
    // is instant too.
    useEffect(() => {
        const urls = [`/${locale}/cv`, ...new Set(services.filter((service) => service.path).map((service) => `/${locale}/paths/${service.path}`))];
        const warm = () => urls.forEach(prefetch);
        const idle = window.requestIdleCallback ? window.requestIdleCallback(warm) : window.setTimeout(warm, 1200);

        return () => (window.cancelIdleCallback ? window.cancelIdleCallback(idle) : window.clearTimeout(idle));
    }, [locale, services]);

    const email = mailto(person.email, t('Quote request'));

    return (
        <div className="site-light min-h-screen">
            <HomeHeader person={person} email={email} />

            <main id="main">
                <Hero person={person} experience={experience} email={email} />
                {services.length > 0 && <Services services={services} />}
                {factors.length > 0 && <Costs factors={factors} />}
                {steps.length > 0 && <Steps steps={steps} />}
                {faqs.length > 0 && <Faq faqs={faqs} />}
                <Contact person={person} email={email} />
            </main>

            <HomeFooter person={person} />
        </div>
    );
}

/**
 * Colours the browser's address bar like the page while this page is open;
 * the shell's default belongs to the dark pages.
 */
function useBrowserColor(color) {
    useEffect(() => {
        const meta = document.querySelector('meta[name="theme-color"]');
        const previous = meta?.getAttribute('content');

        meta?.setAttribute('content', color);

        return () => {
            if (previous) {
                meta?.setAttribute('content', previous);
            }
        };
    }, [color]);
}

function Hero({ person, experience, email }) {
    const { t } = useLocale();
    const photo = Boolean(person.photo_url);

    const facts = [
        experience.years && [experience.years, t('In production')],
        [t('Written quote'), t('Before we start')],
        [t('6 months of support'), t('Included in the price')],
        [t('Remote'), t('In Spanish or English')],
    ].filter(Boolean);

    return (
        <section className={`${wrap} pb-12 pt-10 sm:pt-14 lg:pb-16 lg:pt-20`}>
            <div className={`grid gap-10 ${photo ? 'lg:grid-cols-[1fr_auto] lg:items-center lg:gap-16' : ''}`}>
                <div>
                    <h1 className="font-display text-[clamp(2.25rem,5.6vw,4rem)] font-extrabold leading-[1.04] tracking-[-0.035em] text-balance">
                        {t('Websites, systems and SAP for your business.')}
                    </h1>
                    <p className="mt-6 max-w-[46ch] text-[18px] leading-[1.6] text-soft">
                        {t('I build it, I explain it and I give you a written quote before I start. Rates below.')}
                    </p>
                    <div className="mt-8 flex flex-wrap items-center gap-x-6 gap-y-3">
                        <a href={email} className="btn-ink">
                            {t('Email me')}
                        </a>
                        <a href="#services" className="press rounded-md py-3 text-[16px] font-semibold underline decoration-line-strong underline-offset-[6px] hover:decoration-ink">
                            {t('See rates')}
                        </a>
                    </div>
                </div>

                {photo && (
                    // The uploaded photo is small (200px), so it is shown at that
                    // size instead of stretched, which would blur it.
                    <img
                        src={person.photo_url}
                        alt={person.name}
                        width="200"
                        height="200"
                        fetchPriority="high"
                        className="size-40 rounded-xl border border-line object-cover sm:size-48 lg:size-[200px]"
                    />
                )}
            </div>

            <ul className="mt-12 grid grid-cols-2 gap-x-6 gap-y-6 border-t border-line pt-8 lg:mt-16 lg:grid-cols-4">
                {facts.map(([value, label]) => (
                    <li key={label}>
                        <p className="text-[16px] font-semibold leading-[1.3]">{value}</p>
                        <p className="mt-1 text-[14px] text-muted">{label}</p>
                    </li>
                ))}
            </ul>
        </section>
    );
}

/**
 * One card per rate: what it is, the topics it covers, and the hourly rate.
 * The rate arrives already in the page's currency (dollars in English, pesos
 * converted at the day's rate in Spanish).
 */
function Services({ services }) {
    const { locale, t } = useLocale();
    const converted = services.some((service) => service.rate?.converted);

    return (
        <section id="services" className="border-t border-line">
            <div className={`${wrap} py-16 lg:py-24`}>
                <div className="max-w-2xl">
                    <h2 className={heading}>{t('Services and rates')}</h2>
                    <p className="mt-4 text-[17px] leading-[1.6] text-soft">
                        {t('I charge by the hour. Before we start you get a written quote with the hours I expect.')}
                    </p>
                </div>

                <div className="mt-10 grid gap-5 lg:grid-cols-2">
                    {services.map((service, index) => (
                        <Reveal
                            as="article"
                            key={service.id}
                            index={index}
                            className="flex flex-col gap-6 rounded-xl border border-line bg-raised p-6 sm:p-8 lg:p-10"
                        >
                            <div className="flex-1">
                                <h3 className="font-display text-[26px] font-extrabold leading-[1.1] tracking-[-0.02em] sm:text-[30px]">{service.title}</h3>
                                {service.summary && <p className="mt-3 max-w-[52ch] text-[16px] leading-[1.6] text-soft">{service.summary}</p>}

                                {service.topics.length > 0 && (
                                    <ul className="mt-5 flex flex-wrap gap-2">
                                        {service.topics.map((topic) => (
                                            <li key={topic} className="rounded-md border border-line-strong px-2.5 py-1 text-[14px] text-soft">
                                                {topic}
                                            </li>
                                        ))}
                                    </ul>
                                )}
                            </div>

                            {service.rate && (
                                <div className="border-t border-line pt-6">
                                    <p className="font-display text-[48px] font-extrabold leading-none tracking-[-0.03em] tabular-nums">
                                        {formatMoney(service.rate.amount, service.rate.currency)}
                                    </p>
                                    <p className="mt-2 text-[14px] text-muted">
                                        <span translate="no">{service.rate.currency}</span> {t('per hour')}
                                        {service.rate.converted && <> {t('plus VAT')}</>}
                                    </p>
                                </div>
                            )}

                            {service.path && (
                                <PageLink to={`/${locale}/${service.path}`} className="press -mt-2 inline-block self-start rounded-md py-3 text-[15px] font-semibold text-soft hover:text-ink">
                                    {t('See the work')}
                                </PageLink>
                            )}
                        </Reveal>
                    ))}
                </div>

                <div className="mt-6 flex max-w-[70ch] flex-col gap-1 text-[14px] leading-[1.6] text-muted">
                    <p>
                        {converted ? t('Pesos at the current exchange rate.') : t('Rates in US dollars.')}
                        {converted && (
                            <>
                                {' '}
                                <a
                                    href="https://www.exchangerate-api.com"
                                    rel="noopener noreferrer"
                                    target="_blank"
                                    translate="no"
                                    className="underline decoration-line-strong underline-offset-4 hover:text-ink"
                                >
                                    Rates By Exchange Rate API
                                </a>
                            </>
                        )}
                    </p>
                    <p>{t('Advance of 40% when you approve the quote. Quotes are valid for 15 days. Includes 6 months of support and minor changes.')}</p>
                </div>
            </div>
        </section>
    );
}

/**
 * The topics that add hours to a quote, kept general on purpose.
 */
function Costs({ factors }) {
    const { t } = useLocale();

    return (
        <section className="border-t border-line">
            <div className={`${wrap} py-16 lg:py-24`}>
                <div className="max-w-2xl">
                    <h2 className={heading}>{t('What adds to the cost')}</h2>
                    <p className="mt-4 text-[17px] leading-[1.6] text-soft">
                        {t('These topics add hours when your project needs them. The written quote lists the ones that apply.')}
                    </p>
                </div>

                <ul className="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {factors.map((factor, index) => (
                        <Reveal as="li" key={factor.id} index={index} className="rounded-lg border border-line bg-raised p-5">
                            <h3 className="text-[16px] font-bold leading-[1.3]">{factor.title}</h3>
                            {factor.body && <p className="mt-1.5 text-[14px] leading-[1.55] text-muted">{factor.body}</p>}
                        </Reveal>
                    ))}
                </ul>
            </div>
        </section>
    );
}

function Steps({ steps }) {
    const { t } = useLocale();

    return (
        <section className="border-y border-line bg-raised">
            <div className={`${wrap} py-16 lg:py-24`}>
                <h2 className={heading}>{t('How I work')}</h2>

                <ol className="mt-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-[repeat(var(--cols),minmax(0,1fr))] lg:gap-10" style={{ '--cols': Math.min(steps.length, 4) }}>
                    {steps.map((step, index) => (
                        <Reveal as="li" key={step.id} index={index} className="flex flex-col gap-3 border-t border-ink pt-5">
                            <span className="font-mono text-[13px] font-medium text-signal">{String(index + 1).padStart(2, '0')}</span>
                            <h3 className="text-[18px] font-bold leading-[1.25] tracking-[-0.01em]">{step.title}</h3>
                            {step.body && <p className="text-[15px] leading-[1.6] text-soft">{step.body}</p>}
                        </Reveal>
                    ))}
                </ol>
            </div>
        </section>
    );
}

function Faq({ faqs }) {
    const { t } = useLocale();

    return (
        <section className={`${wrap} py-16 lg:py-24`}>
            <div className="grid gap-10 lg:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)] lg:gap-16">
                <h2 className={heading}>{t('Questions')}</h2>

                <div className="border-t border-line">
                    {faqs.map((faq) => (
                        <details key={faq.id} className="group border-b border-line">
                            <summary className="flex cursor-pointer list-none items-center justify-between gap-6 py-5 text-[17px] font-semibold leading-[1.35] hover:text-soft">
                                {faq.question}
                                <span className="shrink-0 font-mono text-[18px] text-muted" aria-hidden="true">
                                    <span className="group-open:hidden">+</span>
                                    <span className="hidden group-open:inline">-</span>
                                </span>
                            </summary>
                            <p className="max-w-[60ch] pb-6 text-[16px] leading-[1.65] text-soft">{faq.answer}</p>
                        </details>
                    ))}
                </div>
            </div>
        </section>
    );
}

/**
 * The one way in: email. Social profiles and the phone sit beside it as
 * plain links.
 */
function Contact({ person, email }) {
    const { t } = useLocale();
    const links = socials(person, t);

    return (
        <section id="contact" className={`${wrap} pb-16 lg:pb-24`}>
            <div className="rounded-xl border border-line bg-raised p-8 sm:p-12 lg:p-16">
                <h2 className={`${heading} max-w-[16ch]`}>{t('Tell me what you need')}</h2>
                <p className="mt-4 max-w-[52ch] text-[17px] leading-[1.6] text-soft">
                    {t('Write to me with a short description. I reply with questions or a quote.')}
                </p>

                <div className="mt-8 flex flex-wrap items-center gap-x-6 gap-y-2">
                    <a href={email} className="btn-ink">
                        {t('Email me')}
                    </a>
                    <a href={email} className="press rounded-md py-3 text-[15px] text-soft underline decoration-line-strong underline-offset-[6px] [overflow-wrap:anywhere] hover:text-ink">
                        {person.email}
                    </a>
                </div>

                {(links.length > 0 || person.phone) && (
                    <ul className="-mx-3 mt-4 flex flex-wrap items-center text-[15px]">
                        {links.map(([label, url]) => (
                            <li key={label}>
                                <a href={url} rel="noopener noreferrer" target="_blank" className="press block rounded-md px-3 py-3 text-soft hover:text-ink">
                                    {label}
                                </a>
                            </li>
                        ))}
                        {person.phone && (
                            <li>
                                <a href={person.phone_href} className="press block rounded-md px-3 py-3 text-soft hover:text-ink">
                                    {person.phone}
                                </a>
                            </li>
                        )}
                    </ul>
                )}
            </div>
        </section>
    );
}
