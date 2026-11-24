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
            <button
                type="button"
                data-sales-export
                class="inline-flex min-h-11 cursor-pointer items-center justify-center gap-2 self-start rounded-lg bg-ckb-primary px-4 text-xs font-semibold text-ckb-on-primary transition hover:bg-ckb-primary/90 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent disabled:cursor-not-allowed disabled:opacity-60 motion-reduce:transition-none sm:self-auto"
            >
                <span class="inline-flex items-center justify-center">
                    <span data-export-idle>
                        <i class="fas fa-file-pdf" aria-hidden="true"></i>
                    </span>
                    <span data-export-loading hidden>
                        <i
                            class="fas fa-spinner fa-spin hidden"
                            aria-hidden="true"
                        ></i>
                    </span>
                </span>

                <span data-export-label>Unduh PDF</span>
            </button>
        </header>

        <section
            class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3"
            aria-label="Ringkasan penjualan"
        >
            <div class="col-span-full flex justify-end">
                <x-report.segmented-filter
                    group="sales-metrics-period"
                    label="Periode metrik penjualan"
                    :options="[
                        'week' => '7 Hari Terakhir',
                        'month' => 'Bulan Ini',
                        'year' => 'Tahun Ini',
                    ]"
                    selected="month"
                />
            </div>
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
