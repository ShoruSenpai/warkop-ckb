@props ([
    "label",
    "value" => "Memuat...",
    "note" => "",
    "icon",
    "dataKey",
])

<article
    class="flex min-h-28 min-w-0 flex-col justify-between rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface p-4 shadow-surface"
>
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <h3 class="text-xs font-semibold text-ckb-on-surface-variant">
                {{ $label }}
            </h3>
            <p
                data-report-value="{{ $dataKey }}"
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
    <p
        data-report-note="{{ $dataKey }}"
        class="mt-2 text-xs text-ckb-accent"
    >{{ $note }}</p>
</article>
