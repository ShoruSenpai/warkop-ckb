<x-layout title="Laporan Pembelian">
    <div class="mx-auto w-full max-w-[1440px] space-y-4" data-purchase-overview>
        <header
            class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between"
        >
            <div class="min-w-0">
                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-ckb-on-surface-variant">Biaya operasional</p>
                <h1
                    class="text-xl font-bold leading-tight text-ckb-primary sm:text-2xl"
                >
                    Laporan Pembelian
                </h1>
                <p class="mt-1 text-xs text-ckb-on-surface-variant">Pantau pengeluaran dan invoice pembelian bahan baku.</p>
            </div>
        </header>

        <section
            class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3"
            aria-label="Ringkasan pembelian"
            data-purchase-report-card="overview-metrics"
        >
            <div class="col-span-full flex justify-end">
                <x-report.segmented-filter
                    group="purchase-metrics-period"
                    label="Periode metrik pembelian"
                    :options="[
                        'week' => '7 Hari Terakhir',
                        'month' => 'Bulan Ini',
                        'year' => 'Tahun Ini',
                    ]"
                    selected="month"
                />
            </div>
            <x-report.purchase.metric-card
                label="Total pembelian"
                value="Memuat..."
                note="Pada periode terpilih"
                icon="fa-file-invoice-dollar"
                data-key="total_purchases"
            />
            <x-report.purchase.metric-card
                label="Invoice diproses"
                value="Memuat..."
                note="Termasuk semua status"
                icon="fa-truck-loading"
                data-key="invoice_count"
            />
            <x-report.purchase.metric-card
                label="Rata-rata pembelian"
                value="Memuat..."
                note="Per invoice pada periode ini"
                icon="fa-chart-pie"
                data-key="average_purchase"
            />
        </section>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
            <x-report.purchase.spending-chart />
            <x-report.purchase.top-suppliers />
        </div>
    </div>

    <x-slot:script>
        @vite ("resources/js/reports/purchase/overview.js")
    </x-slot:script>
</x-layout>
