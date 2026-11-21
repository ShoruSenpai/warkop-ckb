<x-layout title="Laporan Penjualan">
    <div
        class="mx-auto w-full max-w-[1440px] space-y-4"
        data-sales-report
        data-report-state="empty"
    >
        <header
            class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between"
        >
            <div class="min-w-0">
                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-ckb-on-surface-variant">Kinerja Pendapatan</p>
                <h1
                    class="text-xl font-bold leading-tight text-ckb-primary sm:text-2xl"
                >
                    Laporan Penjualan
                </h1>
                <p class="mt-1 text-xs text-ckb-on-surface-variant">Analisis performa penjualan, transaksi, dan produk terlaris.</p>
            </div>
        </header>

        <section
            class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3"
            aria-label="Ringkasan penjualan"
            data-sales-report-card="metrics"
        >
            <x-report.sales.metric-card
                label="Total penjualan"
                value="—"
                note="Belum tersedia dari sumber transaksi."
                delta="Perbandingan belum tersedia"
                icon="fa-file-invoice-dollar"
                data-key="sales_total"
            />
            <x-report.sales.metric-card
                label="Jumlah transaksi"
                value="—"
                note="Nota selesai belum tersedia."
                delta="Perbandingan belum tersedia"
                icon="fa-cash-register"
                data-key="sales_count"
            />
            <x-report.sales.metric-card
                label="Rata-rata nota"
                value="—"
                note="Menunggu total penjualan dan transaksi."
                delta="Perbandingan belum tersedia"
                icon="fa-chart-line"
                data-key="sales_average"
            />
        </section>
        <div data-metrics-error hidden class="flex items-center justify-end gap-2 text-xs text-ckb-on-error-container">
            <span data-metrics-error-message>Ringkasan penjualan tidak dapat dimuat.</span>
            <button type="button" data-sales-retry="metrics" class="min-h-9 cursor-pointer rounded-lg border border-ckb-outline-variant px-3 font-semibold">Coba lagi</button>
        </div>

        <section
            class="grid grid-cols-1 gap-3 xl:grid-cols-3"
            aria-label="Analisis penjualan"
        >
            <x-report.sales.trend-chart />
            <x-report.sales.top-products />
        </section>

        <x-report.sales.transaction-history />
        <x-report.sales.transaction-detail />
    </div>

    <x-slot:script>
        @vite ("resources/js/reports/sales/index.js")
    </x-slot:script>
</x-layout>
