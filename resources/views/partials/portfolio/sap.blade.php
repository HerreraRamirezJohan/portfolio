@php
    // Modules confirmed against real experience. Keeping this list honest
    // matters more than making it long.
    $modules = [
        ['code' => 'QM', 'name' => __('Quality Management'), 'note' => __('Warehouse and quality process work alongside the business teams.'), 'lead' => true],
        ['code' => 'MM', 'name' => __('Materials Management'), 'note' => __('Logistics flows and material documentation.'), 'lead' => false],
        ['code' => 'SD', 'name' => __('Sales & Distribution'), 'note' => __('Order and delivery documentation.'), 'lead' => false],
        ['code' => 'FI', 'name' => __('Financial Accounting'), 'note' => __('Financial documentation and billing support.'), 'lead' => false],
    ];

    $capabilities = [
        [
            'title' => 'ABAP & OO ABAP',
            'body' => __('Reports, complex data queries, custom tables and forms for logistics and financial documentation.'),
            'tags' => ['User exits', 'BADIs'],
            'icon' => 'code',
        ],
        [
            'title' => __('CDS views & HANA'),
            'body' => __('Reusable query layers - joins, associations, filters and aggregations composed so one view feeds several reports.'),
            'tags' => ['SAP HANA', 'CDS Views'],
            'icon' => 'database',
        ],
        [
            'title' => 'OData & Fiori',
            'body' => __('OData v2/v4 services backing custom front ends and third-party integrations, consumed by SAPUI5 apps.'),
            'tags' => ['SAPUI5', 'Fiori'],
            'icon' => 'link',
        ],
    ];
@endphp

<section id="sap" class="relative border-t border-line bg-elevated px-5 py-24 sm:px-8 sm:py-28 lg:px-12 lg:py-32">
    <div aria-hidden="true"
         class="absolute inset-x-0 top-0 mx-auto h-px w-full max-w-[620px]
                bg-[linear-gradient(90deg,transparent,var(--c-accent),transparent)] opacity-60"></div>

    <div class="mx-auto max-w-[1400px]">
        <div class="mb-14 flex flex-col justify-between gap-6 sm:flex-row sm:items-end">
            <x-ui.section-title :eyebrow="'02 — ' . __('Enterprise')" title="SAP" />
            <p class="reveal max-w-md text-[15.5px] leading-[1.7] text-muted text-pretty sm:text-right">
                {{ __('Custom development and integration across logistics, quality and financial documentation.') }}
            </p>
        </div>

        <div class="mb-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4" data-reveal-group>
            @foreach ($modules as $module)
                <div @class([
                    'reveal rounded-2xl border p-7',
                    'border-accent-line bg-accent-soft' => $module['lead'],
                    'border-line' => ! $module['lead'],
                ])>
                    <p @class([
                        'mb-3 font-mono text-[34px] font-semibold leading-none tracking-[0.04em]',
                        'text-accent' => $module['lead'],
                        'text-ink/85' => ! $module['lead'],
                    ])>{{ $module['code'] }}</p>
                    <p class="text-[14.5px] font-semibold">{{ $module['name'] }}</p>
                    <p class="mt-1.5 text-[13.5px] leading-[1.6] text-muted text-pretty">{{ $module['note'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid gap-5 lg:grid-cols-3" data-reveal-group>
            @foreach ($capabilities as $cap)
                <div class="reveal lift rounded-2xl border border-line bg-surface p-7">
                    <span class="text-accent">
                        <svg class="size-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            @switch($cap['icon'])
                                @case('code')
                                    <path d="M8 6 3 12l5 6M16 6l5 6-5 6M13.5 4 10.5 20"/>
                                    @break
                                @case('database')
                                    <ellipse cx="12" cy="6" rx="8" ry="3"/>
                                    <path d="M4 6v12c0 1.7 3.6 3 8 3s8-1.3 8-3V6"/>
                                    <path d="M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3"/>
                                    @break
                                @case('link')
                                    <path d="M10 13a5 5 0 0 0 7.5.5l3-3a5 5 0 0 0-7-7l-1.7 1.7"/>
                                    <path d="M14 11a5 5 0 0 0-7.5-.5l-3 3a5 5 0 0 0 7 7l1.7-1.7"/>
                                    @break
                            @endswitch
                        </svg>
                    </span>
                    <h3 class="mb-2.5 mt-4 text-[17.5px] font-semibold">{{ $cap['title'] }}</h3>
                    <p class="mb-4 text-[14.5px] leading-[1.7] text-muted text-pretty">{{ $cap['body'] }}</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($cap['tags'] as $tag)
                            <span class="rounded-md border border-line px-2.5 py-1 font-mono text-[11.5px] text-muted">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
