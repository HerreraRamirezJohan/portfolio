# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A single-user Laravel portfolio/CV site: a JSON API (`routes/api.php`) consumed by a React SPA (`resources/js`) in the same project, for both the public site and the admin panel. There is no registration route and no multi-tenancy — the one `User` row comes from `CvSeeder`, seeded from `database/data/cv.json` (transcribed from an external CV generator). The public site is bilingual (`/es`, `/en`): the home page sells the owner as a freelancer (services, process, FAQ) and the CV lives at `/{locale}/cv`; the admin panel lets that one user edit all of it.

## Commands

```sh
# Tests (PHPUnit, not Pest)
php artisan test
vendor/bin/phpunit
vendor/bin/phpunit --filter test_spanish_page_renders_the_cv
vendor/bin/phpunit tests/Feature/PortfolioPageTest.php

# Local dev (serves app + queue listener + Pail logs + vite concurrently)
composer dev

# Frontend build
npm run dev      # vite dev server
npm run build    # production assets

# Formatting
vendor/bin/pint
```

Tests run against an in-memory SQLite DB (`phpunit.xml` sets `DB_DATABASE=:memory:`) and set `ADMIN_PASSWORD=test-password`, since `CvSeeder` refuses to run without one.

## Architecture

**Shape.** Laravel serves JSON only; React renders every page. `routes/web.php` returns the one Blade view (`resources/views/app.blade.php`) via `SpaController` — it server-renders just the `<head>` (title, description, hreflang from `AppSupportSiteMeta`) so crawlers see them, passes `{locales, defaultLocale, paths}` to React through `#app[data-config]`, and inlines the page's own API payload in `<script id="bootstrap">` so a deep link renders without a first request. Web routes stay explicit (not a catch-all) so unknown locales/paths still 404 on the server.

**Locale routing.** Every public route — web and API — is prefixed with `{locale}` (`es`|`en`, from `config/portfolio.php`). `SetLocale` middleware validates the segment, calls `app()->setLocale()`, then strips the parameter so controllers never see it. `/` redirects to `/{app.locale}`. Public API: `GET /api/{locale}/home`, `GET /api/{locale}/cv` and `GET /api/{locale}/paths/{path}` (`ApiPortfolioController`), which return content already translated into that locale.

**Public content cache.** `App\Support\PublicContent` builds each public payload as a JSON string and caches it per locale (`portfolio.cache_ttl`, env `PORTFOLIO_CACHE_TTL`, default 3600; 0 disables). The same string answers the API and is inlined in the shell. The `FlushPublicContent` middleware on `/api/admin/*` clears every key after a successful write, and `CvSeeder` does the same; anything that changes content another way (tinker, a deploy that edits `config/portfolio.php` or `lang/`) needs `php artisan cache:clear` or waits out the TTL. The public API group drops Sanctum's stateful middleware (no session, no cookie) and sends `Cache-Control: public, max-age=0, must-revalidate` plus an ETag, so repeat requests are 304s.

**Auth.** Sanctum SPA authentication (cookie session, no API tokens): `statefulApi()` in `bootstrap/app.php`, and `config/sanctum.php` includes `Sanctum::currentRequestHost()` because the SPA is same-origin. The React login GETs `/sanctum/csrf-cookie`, then `POST /api/login` (`ApiSessionController`, `web` guard, rate limited); axios (`resources/js/lib/http.js`) sends the cookie and `X-XSRF-TOKEN`, and redirects to `/admin/login` on 401/419. `/admin/*` shell routes are also behind the `auth` middleware. Admin data lives under `/api/admin/*` behind `auth:sanctum`. In tests, login requests need a `Referer` of `url('/')` to be stateful; otherwise use `actingAs()`.

**Translatable content.** Every content model uses `spatie/laravel-translatable`'s `HasTranslations` (`Profile`, `WorkExperience`, `Education`, `SkillGroup`, `Language`, `Project`, `Service`, `ProcessStep`, `Faq`, `CostFactor`). Public API resources (`app/Http/Resources/*`) return plain strings in the active locale; admin resources (`app/Http/Resources/Admin/*`) return every translatable field as a complete `{es, en}` map via `Translations::fill()`. On write, Form Requests (`app/Http/Requests/Admin/*`) build attributes with `Translations::clean()`, and updates must go through `Translations::update()` — a plain `update()` *merges* locale maps, so a blanked translation would survive.

**Single-user data model.** `User` hasMany/hasOne everything (`profile`, `workExperiences`, `educations`, `skillGroups.skills`, `languages`, `projects`, `services`, `processSteps`, `faqs`, `costFactors`), all ordered by a `sort_order` column via an `ordered()` query scope. There's exactly one user in the DB at any time; controllers fetch it with `User::firstOrFail()` rather than scoping to `auth()->id()`.

**Admin API** (`app/Http/Controllers/Api/Admin`). Each ordered collection (`experiences`, `educations`, `skill-groups`, `languages`, `projects`, `services`, `process-steps`, `faqs`, `cost-factors`) is an `apiResource` without `show` plus `PATCH {name}/{id}/move` (`{direction: up|down}`, returns the reordered index). Controllers use the `MovesRecords` trait and `AppSupportSortOrder` (swap-and-rewrite the whole run, so duplicated/sparse sort values still work). Follow `ProjectController` + `ProjectRequest` + `AdminProjectResource` as the reference when adding a collection. On the React side, every collection screen is configuration for `resources/js/admin/CollectionManager.jsx` (see `admin/managers.jsx`); the ES|EN tab is `?lang=`.

**Public site** (`resources/js/pages`). `Home.jsx` (`/{locale}`) — the freelance landing page, plain and light (`.site-light`): hero, two service cards (title, summary, topic tags and an hourly rate), "what adds to the cost" (the `CostFactor` list), how the work runs, FAQ and contact. There are no prices by project size and no WhatsApp: the one action is email (`mailto()` in `site/Contact.jsx`), with social links and the phone beside it. Its own `site/HomeHeader.jsx` and `site/HomeFooter.jsx` replace `Nav.jsx` and the Ledger footer. Nav links to `/{locale}#services` work from any page; `PublicLayout` scrolls to a hash after client-side navigation. `Cv.jsx` (`/{locale}/cv`) — the CV: hero, pipelines (each path's flow, linking to its page), the experience ledger, education/languages, case files (published projects), stack index (skill groups), contact. `CareerPath.jsx` (`/{locale}/{path}`) — one page per career path (`sap`, `web`, `infra`), reached from the CV pipelines and from a service's "See the work" link. All three share `site/Nav.jsx`, `site/Contact.jsx` and `site/Blocks.jsx` under `site/PublicLayout.jsx`. Sections with no data (no projects, no skill groups) are not rendered.

Public pages read data with `usePage(url)` (`site/PublicLayout.jsx`), which suspends on a promise from `lib/cache.js` — an in-memory map seeded from the inlined payload, 5-minute TTL. Link to public pages with `site/PageLink.jsx`, not `<Link>`: it prefetches the target's payload on hover/focus/touch. `lib/useApi.js` is for the admin only. The admin is a lazy chunk (`admin/AdminRoutes.jsx`).

**Page transitions.** `PublicLayout` wraps the outlet in React's `<ViewTransition default="page">`; because React Router navigates inside a transition and pages suspend, the old page stays up until the next has data, then the View Transitions API animates (styles under "Page transitions" in `app.css`). `<ViewTransition name="path-{key}">` on a path's red focus node (`FlowBox` in `site/Blocks.jsx`), in its CV pipeline and in its page's flow, makes that box travel between the two; `site-nav` keeps the bar still. Give a name to at most one mounted element at a time. Browser Back/Forward does not animate (React skips popstate).

Path copy (titles, services, flow diagram, quote, per-company blurbs) lives in `config/portfolio.php` under `paths`, written in English and translated via `lang/es.json` server-side (`CareerPathResource`). `AppSupportCareerPaths` joins it to the DB: a path's roles are the `WorkExperience` rows whose `company` is a key in its `roles` map (renaming a company in the admin drops it from the path), and published projects join a path when a `tech_stack` entry matches its `stack` list case-insensitively.

**Hourly rates and currency.** Rates are stored in USD. `ServiceResource` outputs `rate: {amount, currency, converted}` through `App\Support\HourlyRates`, which picks the currency from `portfolio.currencies` (`es` -> MXN, `en` -> USD): dollars pass through untouched, pesos are `USD x rate` rounded to 5. The rate comes from `App\Support\ExchangeRate` (ExchangeRate-API open endpoint, no key): cached `services.exchange_rate.ttl`, the last good value is kept for good and reused when the API fails, and `services.exchange_rate.fallback` applies if it never worked, so a page never breaks on it. The Spanish page must show the credit link "Rates By Exchange Rate API" (`Home.jsx` does, only when a rate is converted). `tests/TestCase.php` calls `Http::preventStrayRequests()`; fake the endpoint (`open.er-api.com/*`) in a test that needs a specific rate.

**UI strings.** React imports `lang/en.json` / `lang/es.json` directly (`resources/js/lib/i18n.jsx`): `t('English key')`, `choice()` for `one|many`, and `<Trans>` for placeholders that are React nodes. Add new UI strings to `lang/es.json` like any `__()` key.

Styling: the CV and the career-path pages are "Ledger", dark only — industrial telemetry, square corners, visible 1px grid, one accent. The home page is the exception: `.site-light` (in `app.css`, set on `Home.jsx`'s root, inside `.site`) redefines the same `--c-*` variables with a warm off-white ground and a darker red, so the same Tailwind colour utilities work; it uses the sans face, sentence-case headings, 8-12px corners and the `.btn-ink` / `.btn-line` buttons, and does not use `.macro`, `.cells` or the mono labels. Everything below about Ledger applies to the dark pages. CSS custom properties in `resources/css/app.css` are exposed as Tailwind colors (`bg-base`, `text-ink`, `text-soft`, `text-muted`, `border-line`, …); `signal` (hazard red) is the only accent, `live` green is reserved for the single availability dot, and `faint` is decorative only (fails AA for text). Primitives live there too: `.cells` (a grid whose 1px gap draws the dividers — children get the ground colour), `.macro` (heavy expanded caps), `.label` (small uppercase mono), `.btn-signal`, `.press`, `.frame` (page width and gutters). `.site` (set by `PublicLayout` and `NotFound`) scopes the palette, mono body type and `color-scheme: dark`; the admin keeps its own stone palette and follows the OS theme through `dark:`. Use `components/Macro.jsx` with `fit` for display headings: it sizes the type from the longest word so Spanish never overflows. No em-dashes in page copy (use hyphens). Never use `text-base` — it collides with the `base` colour; write `text-[16px]`.

**Tech icons.** `AppSupportTechIcon` maps known skill/stack names (lowercased, exact match) to an icon slug; the API sends stacks as `{name, icon}` chips (`TechIcon::chips()`) and `components/TechChip.jsx` draws the mark. Names with no mapping render as plain text chips by design — don't add a catch-all fallback icon. PHP and Linux use their real logos (`resources/img/logos`, credited in `site/Contact.jsx`: PHP logo CC BY-SA 4.0, Tux by Larry Ewing). SAP-family entries (ABAP, Fiori, CDS views, etc.) all map to a single neutral `sap` glyph — never the SAP logo: SAP's trademark policy forbids third-party use without written permission, and ABAP has no logo.

**Seeding.** `CvSeeder` is idempotent (`updateOrCreate` throughout) and safe to re-run: it never overwrites an admin-uploaded profile photo with a missing one, and projects are seeded once from `cv.json` but are expected to be managed afterward through the admin panel. `FreelanceSeeder` (copy and prices in `database/data/freelance.json`) fills services, process steps and FAQs, each only while empty. A service stores its hourly rate in US dollars (`rate_usd`, edited in the admin Services form); the starter rates ($15 web, $20 SAP) match `E:\Documents\Cotizaciones` (`Tarifas_Web/Tarifas_Web_ES.md` section 8). `CvSeeder` calls it only when it creates the user (a fresh install), so re-seeding the CV never brings back what the admin deleted; an existing database runs it once with `db:seed --class=FreelanceSeeder` (no password needed). It requires `ADMIN_PASSWORD` in the environment — this is intentional; the password is never hardcoded.

## Frontend

React 19 + React Router (`resources/js/app.jsx` entry, `@vitejs/plugin-react`, `@viteReactRefresh` in the shell), axios, Tailwind CSS v4 (via `@tailwindcss/vite`) and Vite. Plain JSX, no TypeScript. Fonts are self-hosted through `laravel-vite-plugin/fonts`, configured in `vite.config.js`: Archivo for display (`local()`, a variable woff2 with the `wdth` axis in `resources/fonts/archivo`, OFL — the remote providers drop the width axis `.macro` needs) and JetBrains Mono (`bunny`) for everything else. `node_modules` is installed inside Sail (Linux native bindings, owned by root), so install/build there: `docker compose exec laravel.test npm run build`.

There are design-taste and web-design-guidelines skills available under `.agents/skills/` — use them for frontend/UI work in this repo.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

## Foundational Context

This application is a Laravel application running on PHP 8.5. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `vendor/bin/sail composer show --direct` to list direct dependencies with versions, or `vendor/bin/sail composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If a frontend change doesn't show in the UI or you get a "Unable to locate file in Vite manifest" error, run `vendor/bin/sail npm run build` or ask the user to run `vendor/bin/sail npm run dev` or `vendor/bin/sail composer run dev`.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists, including path-scoped framework guidelines under `.ai/rules/boost`. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.

## Artisan

- Run Artisan commands directly via the command line (e.g., `vendor/bin/sail artisan route:list`). Use `vendor/bin/sail artisan list` to discover available commands and `vendor/bin/sail artisan [command] --help` to check parameters.
- Inspect routes with `vendor/bin/sail artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `vendor/bin/sail artisan config:show app.name`, `vendor/bin/sail artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `vendor/bin/sail artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `vendor/bin/sail artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.
- Activate the `deploying-to-cloud` skill whenever deploying to Laravel Cloud, configuring Cloud environments or resources, using the Cloud CLI, or troubleshooting Cloud deployments.

=== sail rules ===

# Laravel Sail

- This project runs inside Laravel Sail's Docker containers. You MUST execute all commands through Sail.
- Start services using `vendor/bin/sail up -d` and stop them with `vendor/bin/sail stop`.
- Open the application in the browser by running `vendor/bin/sail open`.
- Always prefix PHP, Artisan, Composer, and Node commands with `vendor/bin/sail`. Examples:
    - Run Artisan Commands: `vendor/bin/sail artisan migrate`
    - Install Composer packages: `vendor/bin/sail composer install`
    - Execute Node commands: `vendor/bin/sail npm run dev`
    - Execute PHP scripts: `vendor/bin/sail php [script]`
- View all available Sail commands by running `vendor/bin/sail` without arguments.

=== tests rules ===

# Test Enforcement

- Add or update tests for behavior and logic changes when a test provides meaningful regression coverage.
- Pure copy, styling, and layout-only changes do not require new or updated tests.
- When test coverage applies, run the affected tests and ensure they pass.
- Test the changed behavior and its important failure modes, but do not add tests beyond them.
- Read the `testing-best-practices` skill before writing tests.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `vendor/bin/sail artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `vendor/bin/sail artisan list` and check their parameters with `vendor/bin/sail artisan [command] --help`.
- If you're creating a generic PHP class, use `vendor/bin/sail artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `vendor/bin/sail artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `vendor/bin/sail artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/sail bin pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/sail bin pint --test --format agent`, simply run `vendor/bin/sail bin pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This project uses PHPUnit. Create tests with `vendor/bin/sail artisan make:test --phpunit {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `vendor/bin/sail artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/sail bin phpunit` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.

</laravel-boost-guidelines>
