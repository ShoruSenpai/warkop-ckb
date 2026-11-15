<x-layout title="Rekap Invoice Pembelian">
    <div
        class="mx-auto w-full max-w-[1440px] space-y-4"
        data-purchase-invoices
        data-detail-url-template="{{ route('reports.purchases.show', ['purchase' => '__PURCHASE__']) }}"
    >
        <header
            class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between"
        >
            <div class="min-w-0">
                <a
                    href="{{ route('reports.purchases.index') }}"
                    class="inline-flex min-h-11 cursor-pointer items-center gap-2 text-xs font-semibold text-ckb-primary transition hover:text-ckb-accent focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent motion-reduce:transition-none"
                >
                    <i class="fas fa-arrow-left" aria-hidden="true"></i>
                    Kembali ke laporan pembelian
                </a>
                <p class="mt-4 text-[10px] font-bold uppercase tracking-[0.14em] text-ckb-on-surface-variant">Rekap pembelian</p>
                <h1
                    class="text-xl font-bold leading-tight text-ckb-primary sm:text-2xl"
                >
                    Rekap Invoice Pembelian
                </h1>
                <p data-invoice-summary class="mt-1 text-xs text-ckb-on-surface-variant" role="status">Memuat invoice pembelian...</p>
            </div>
            <label
                class="flex min-h-11 flex-col gap-1 text-[10px] font-bold uppercase tracking-[0.1em] text-ckb-on-surface-variant"
            >
                Periode
                <select
                    data-invoice-period
                    class="min-h-11 min-w-40 cursor-pointer rounded-lg border border-ckb-outline-variant bg-ckb-surface px-3 text-xs font-semibold normal-case tracking-normal text-ckb-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent sm:text-sm"
                >
                    <option value="week">7 Hari Terakhir</option>
                    <option value="month" selected>Bulan Ini</option>
                    <option value="year">Tahun Ini</option>
                </select>
            </label>
        </header>

        <x-report.purchase.invoice-table />
        <template id="invoice-row-template"
            ><x-report.purchase.invoice-row
        /></template>
    </div>

    <x-slot:script>
        @vite ("resources/js/reports/purchase/index.js")
    </x-slot:script>
</x-layout>
