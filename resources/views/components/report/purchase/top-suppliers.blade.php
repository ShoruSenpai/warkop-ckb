<section
    class="min-w-0 rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface p-4 shadow-surface sm:col-span-2 xl:col-span-1"
    aria-labelledby="top-suppliers-title"
>
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-ckb-on-surface-variant">Supplier teratas</p>
            <h3
                id="top-suppliers-title"
                class="mt-1 text-sm font-bold text-ckb-primary"
            >
                Pembelian tertinggi
            </h3>
        </div>
        <a
            href="{{ route('reports.purchases.invoices') }}"
            class="inline-flex min-h-11 cursor-pointer items-center gap-2 rounded-lg bg-ckb-secondary-container px-3 text-xs font-semibold text-ckb-on-secondary-container transition hover:bg-ckb-secondary hover:text-ckb-on-secondary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent motion-reduce:transition-none"
        >
            Lihat invoice
            <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
        </a>
    </div>
    <ol
        data-top-suppliers
        class="mt-3 divide-y divide-ckb-outline-variant/40"
        aria-live="polite"
    >
        <li class="py-6 text-center text-xs text-ckb-on-surface-variant">
            Memuat supplier...
        </li>
    </ol>
</section>
