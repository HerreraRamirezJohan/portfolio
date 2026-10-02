<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin password
    |--------------------------------------------------------------------------
    |
    | Used only by CvSeeder when creating the single admin user. Kept out of
    | the codebase deliberately -- set ADMIN_PASSWORD in .env before seeding.
    |
    */
    'admin_password' => env('ADMIN_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Supported locales
    |--------------------------------------------------------------------------
    |
    | Drives the public URL prefix (/es, /en) and the ES|EN tabs in the admin.
    |
    */
    'locales' => ['es', 'en'],

    /*
    |--------------------------------------------------------------------------
    | Public content cache
    |--------------------------------------------------------------------------
    |
    | Seconds the public pages' JSON is kept (see App\Support\PublicContent).
    | Admin writes clear it at once; the TTL only bounds staleness from things
    | the admin does not touch, like a deploy that changes this file. 0 turns
    | the cache off.
    |
    */
    'cache_ttl' => (int) env('PORTFOLIO_CACHE_TTL', 3600),

    /*
    |--------------------------------------------------------------------------
    | Career paths
    |--------------------------------------------------------------------------
    |
    | Each key is a public route (/{locale}/{key}). Copy is written in English
    | and translated through lang/es.json. Dates, companies and projects still
    | come from the database: `roles` is keyed by WorkExperience::company, and
    | a published project joins a path when its tech stack shares a name with
    | the path's `stack`.
    |
    */
    'paths' => [
        'sap' => [
            'number' => '01',
            'nav' => 'SAP',
            'title' => 'SAP & enterprise systems',
            'card_title' => 'SAP & enterprise',
            'card' => 'OData APIs, CDS views and Fiori apps on SAP HANA.',
            'lead' => 'Backend work on SAP: APIs, query layers and Fiori front ends, designed around the business process first.',
            'highlights' => ['ABAP', 'OData', 'CDS', 'Fiori'],
            'stack' => ['ABAP', 'OO ABAP', 'OData v2', 'OData v4', 'CDS Views', 'SAP HANA', 'SAPUI5', 'Fiori'],
            'facts' => ['Modules' => 'QM · Logistics · Finance'],
            'services' => [
                'OData APIs' => 'v2 and v4 services behind custom front ends and third-party integrations.',
                'CDS views' => 'Reusable query layers: one well-built view feeds several reports and endpoints.',
                'SAPUI5 / Fiori' => 'Apps that consume those APIs, built and deployed end to end.',
                'ABAP & extensions' => 'Custom reports, tables and forms; user exits and BADIs that keep upgrades safe.',
            ],
            'flow' => [
                'title' => 'How the pieces connect',
                'caption' => 'data → screen',
                'nodes' => [['Data', 'SAP HANA'], ['Query', 'CDS views'], ['API', 'OData v2 / v4'], ['UI', 'SAPUI5 / Fiori']],
                'focus' => 3,
                'tags' => ['User exits · BADIs'],
                'note' => 'Extend vendor software to fit the process without breaking upgradeability.',
            ],
            'quote' => 'Most of what breaks a project is a business rule nobody wrote down.',
            'quote_note' => 'I read the functional spec end to end and walk the process with its users before estimating.',
            'roles' => [
                'MAVI de Occidente' => 'OData services, CDS views and Fiori apps; FSD review before every estimate.',
                'Global Asiática' => 'Custom ABAP for logistics and finance, around the QM module; tickets solved in-house.',
            ],
            'extras' => [],
        ],

        'web' => [
            'number' => '02',
            'nav' => 'Web',
            'title' => 'Web development',
            'card_title' => 'Web development',
            'card' => 'Laravel and React apps, from the data model to the screen.',
            'lead' => 'Full stack apps in PHP, Laravel and React, from the data model and REST endpoints to the screen.',
            'highlights' => ['PHP', 'Laravel', 'React', 'PostgreSQL'],
            'stack' => ['PHP', 'Laravel', 'React', 'Ionic', 'JavaScript', 'PostgreSQL', 'MySQL', 'Python', 'Java'],
            'facts' => ['Built' => 'ERP · Billing · HR'],
            'services' => [
                'Laravel apps' => 'HR and ticketing modules with React front ends. This site runs on Laravel too.',
                'ERP & invoicing' => 'A multi-company ERP in native PHP over PostgreSQL, with CFDI stamping.',
                'Mobile with Ionic' => 'Multiplatform apps from a single React + Ionic codebase.',
                'Integrations' => 'Bridge modules in C#, VB and PHP between systems that didn’t talk.',
            ],
            'flow' => [
                'title' => 'How the pieces connect',
                'caption' => 'screen → data',
                'nodes' => [['UI', 'React / Ionic'], ['API', 'REST'], ['App', 'Laravel / PHP'], ['Data', 'PostgreSQL']],
                'focus' => 2,
                'tags' => ['PAC · SOAP / REST'],
                'note' => 'CFDI tax stamping for invoices, verified and synced by cron jobs.',
            ],
            'quote' => 'Two or three options on the table before writing code.',
            'quote_note' => 'I walk the real workflow with the people who will use it. Noticeably less rework at the end.',
            'roles' => [
                'Global Asiática' => 'ERP and billing platform, Laravel + React modules, Ionic apps.',
                'Operadora Macrobús' => 'Logistics software upkeep and bridge modules between the ERP and other systems.',
                'Universidad de Guadalajara (CUCSH)' => 'Laravel 9 app for academic scheduling; Python ETL from Excel into SQL.',
            ],
            'extras' => [],
        ],

        'infra' => [
            'number' => '03',
            'nav' => 'Infra',
            'title' => 'Servers & infrastructure',
            'card_title' => 'Servers & infrastructure',
            'card' => 'Linux, Docker and Nginx, set up to be reproducible.',
            'lead' => 'Linux servers and Docker stacks, set up so environments are reproducible instead of hand-built.',
            'highlights' => ['Linux', 'Docker', 'Nginx', 'PHP-FPM'],
            'stack' => ['Linux', 'Docker', 'Docker Compose', 'Nginx', 'Apache', 'PHP-FPM', 'cron', 'Git', 'Azure'],
            'facts' => ['Setup' => 'Docker Compose', 'Servers' => 'Linux · Nginx · Apache'],
            'services' => [
                'Docker & Compose' => 'Stacks for the app, the database and the services around them.',
                'Linux administration' => 'Users, permissions and services on the servers those stacks run on.',
                'Web servers' => 'Nginx and Apache in front of PHP-FPM.',
                'Operations' => 'Cron jobs, backups, migrations and deployments.',
            ],
            'flow' => [
                'title' => 'A typical stack I run',
                'caption' => 'request → data',
                'nodes' => [['Client', 'Browser'], ['Proxy', 'Nginx'], ['App', 'PHP-FPM · Laravel'], ['Data', 'PostgreSQL']],
                'focus' => 2,
                'group' => ['label' => 'docker compose', 'from' => 1],
                'tags' => ['cron', 'backups', 'deploys'],
                'note' => 'Scheduled jobs, backups and deployments around the stack.',
            ],
            'quote' => 'Reproducible environments, not hand-built ones.',
            'quote_note' => 'If a server dies, the Compose file and the backups are enough to bring it back.',
            'roles' => [
                'Global Asiática' => 'Docker stacks and the Linux servers they ran on: Nginx/Apache, PHP-FPM, cron, backups, deploys.',
            ],
            'extras' => [
                ['when' => '2026', 'title' => 'This portfolio', 'body' => 'A Laravel API with a React front end, containerised with Docker Compose.'],
            ],
        ],
    ],
];
