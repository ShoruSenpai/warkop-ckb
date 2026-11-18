@props ([
    "label",
    "value" => "—",
    "note" => "",
    "delta" => "Perbandingan belum tersedia",
    "icon",
    "dataKey",
])

<article
    class="flex min-h-36 min-w-0 flex-col justify-between rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface-container-lowest p-4 shadow-surface"
>
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <h2 class="text-xs font-semibold text-ckb-on-surface-variant">
                {{ $label }}
            </h2>
            <p
                data-sales-kpi-value="{{ $dataKey }}"
                class="mt-2 break-words text-lg font-bold tracking-tight text-ckb-primary sm:text-xl"
            >{{ $value }}</p>
        </div>
        <span
            class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-ckb-secondary-container text-ckb-primary"
            aria-hidden="true"
        >
            <i class="fas {{ $icon }}"></i>
        </span>
    </div>
    <div
        class="mt-3 flex flex-wrap items-center justify-between gap-x-2 gap-y-1"
    >
        <p
            data-sales-kpi-note="{{ $dataKey }}"
            class="text-xs text-ckb-on-surface-variant"
        >{{ $note }}</p>
        <p
            data-sales-kpi-delta="{{ $dataKey }}"
            class="text-[10px] font-semibold text-ckb-on-surface-variant"
        >{{ $delta }}</p>
    </div>
</article>
