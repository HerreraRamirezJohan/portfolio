# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A single-user Laravel portfolio/CV site: a JSON API (`routes/api.php`) consumed by a React SPA (`resources/js`) in the same project, for both the public site and the admin panel. There is no registration route and no multi-tenancy — the one `User` row comes from `CvSeeder`, seeded from `database/data/cv.json` (transcribed from an external CV generator). The public site renders that user's CV bilingually (`/es`, `/en`); the admin panel lets that one user edit it.

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

**Shape.** Laravel serves JSON only; React renders every page. `routes/web.php` returns the one Blade view (`resources/views/app.blade.php`) via `SpaController` — it server-renders just the `<head>` (title, description, hreflang from `AppSupportSiteMeta`) so crawlers see them, and passes `{locales, defaultLocale, paths}` to React through `#app[data-config]`. Web routes stay explicit (not a catch-all) so unknown locales/paths still 404 on the server.

**Locale routing.** Every public route — web and API — is prefixed with `{locale}` (`es`|`en`, from `config/portfolio.php`). `SetLocale` middleware validates the segment, calls `app()->setLocale()`, then strips the parameter so controllers never see it. `/` redirects to `/{app.locale}`. Public API: `GET /api/{locale}/portfolio` and `GET /api/{locale}/paths/{path}` (`ApiPortfolioController`), which return content already translated into that locale.

**Auth.** Sanctum SPA authentication (cookie session, no API tokens): `statefulApi()` in `bootstrap/app.php`, and `config/sanctum.php` includes `Sanctum::currentRequestHost()` because the SPA is same-origin. The React login GETs `/sanctum/csrf-cookie`, then `POST /api/login` (`ApiSessionController`, `web` guard, rate limited); axios (`resources/js/lib/http.js`) sends the cookie and `X-XSRF-TOKEN`, and redirects to `/admin/login` on 401/419. `/admin/*` shell routes are also behind the `auth` middleware. Admin data lives under `/api/admin/*` behind `auth:sanctum`. In tests, login requests need a `Referer` of `url('/')` to be stateful; otherwise use `actingAs()`.

**Translatable content.** Every content model uses `spatie/laravel-translatable`'s `HasTranslations` (`Profile`, `WorkExperience`, `Education`, `SkillGroup`, `Language`, `Project`). Public API resources (`app/Http/Resources/*`) return plain strings in the active locale; admin resources (`app/Http/Resources/Admin/*`) return every translatable field as a complete `{es, en}` map via `Translations::fill()`. On write, Form Requests (`app/Http/Requests/Admin/*`) build attributes with `Translations::clean()`, and updates must go through `Translations::update()` — a plain `update()` *merges* locale maps, so a blanked translation would survive.

**Single-user data model.** `User` hasMany/hasOne everything (`profile`, `workExperiences`, `educations`, `skillGroups.skills`, `languages`, `projects`), all ordered by a `sort_order` column via an `ordered()` query scope. There's exactly one user in the DB at any time; controllers fetch it with `User::firstOrFail()` rather than scoping to `auth()->id()`.

**Admin API** (`app/Http/Controllers/Api/Admin`). Each ordered collection (`experiences`, `educations`, `skill-groups`, `languages`, `projects`) is an `apiResource` without `show` plus `PATCH {name}/{id}/move` (`{direction: up|down}`, returns the reordered index). Controllers use the `MovesRecords` trait and `AppSupportSortOrder` (swap-and-rewrite the whole run, so duplicated/sparse sort values still work). Follow `ProjectController` + `ProjectRequest` + `AdminProjectResource` as the reference when adding a collection. On the React side, every collection screen is configuration for `resources/js/admin/CollectionManager.jsx` (see `admin/managers.jsx`); the ES|EN tab is `?lang=`.

**Public site** (`resources/js/pages`). `Portfolio.jsx` (`/{locale}`) — hero, three path cards, compact experience list, education/languages, contact. `CareerPath.jsx` (`/{locale}/{path}`) — one page per career path (`sap`, `web`, `infra`). Both share `site/Nav.jsx` and `site/Contact.jsx` under `site/PublicLayout.jsx`.

Path copy (titles, services, flow diagram, quote, per-company blurbs) lives in `config/portfolio.php` under `paths`, written in English and translated via `lang/es.json` server-side (`CareerPathResource`). `AppSupportCareerPaths` joins it to the DB: a path's roles are the `WorkExperience` rows whose `company` is a key in its `roles` map (renaming a company in the admin drops it from the path), and published projects join a path when a `tech_stack` entry matches its `stack` list case-insensitively.

**UI strings.** React imports `lang/en.json` / `lang/es.json` directly (`resources/js/lib/i18n.jsx`): `t('English key')`, `choice()` for `one|many`, and `<Trans>` for placeholders that are React nodes. Add new UI strings to `lang/es.json` like any `__()` key.

Styling: CSS custom properties in `resources/css/app.css` exposed as Tailwind colors (`bg-base`, `text-ink`, `text-muted`, …). Wrapping an element in `.theme-sap|web|infra` scopes `text-path`/`bg-path`/`bg-path-soft` to that path's colour. Never use `text-base` — it collides with the `base` colour; write `text-[16px]`.

**Tech icons.** `AppSupportTechIcon` maps known skill/stack names (lowercased, exact match) to an icon slug; the API sends stacks as `{name, icon}` chips (`TechIcon::chips()`) and `components/TechChip.jsx` draws the mark. Names with no mapping render as plain text chips by design — don't add a catch-all fallback icon. SAP-family entries (ABAP, Fiori, CDS views, etc.) all map to a single `sap` slug.

**Seeding.** `CvSeeder` is idempotent (`updateOrCreate` throughout) and safe to re-run: it never overwrites an admin-uploaded profile photo with a missing one, and projects are seeded once from `cv.json` but are expected to be managed afterward through the admin panel. It requires `ADMIN_PASSWORD` in the environment — this is intentional; the password is never hardcoded.

## Frontend

React 19 + React Router (`resources/js/app.jsx` entry, `@vitejs/plugin-react`, `@viteReactRefresh` in the shell), axios, Tailwind CSS v4 (via `@tailwindcss/vite`) and Vite. Plain JSX, no TypeScript. Fonts (Schibsted Grotesk, JetBrains Mono) are self-hosted through `laravel-vite-plugin/fonts` (`bunny`), configured in `vite.config.js`. `node_modules` is installed inside Sail (Linux native bindings, owned by root), so install/build there: `docker compose exec laravel.test npm run build`.

There are design-taste and web-design-guidelines skills available under `.agents/skills/` — use them for frontend/UI work in this repo.
